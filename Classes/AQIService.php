<?php
namespace RTAQI\Classes;
use Exception;
use PDO;
use PDOException;
use RTAQI\Framework\Classes\Db;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AQIService
{
    private $apiKey = DATA_GOV_API_RTAQI;
    private $apiEndpoint = "https://api.data.gov.in/resource/3b01bcb8-0b14-4abf-b6f2-c1bfd384ba69";
    private $httpClient;
    private $apiUrl;
    private $db;
    private $con;
    private $data;
    public function __construct()
    {
        $this->httpClient = HttpClient::create();
        $this->apiUrl = $this->apiEndpoint . "?api-key=".$this->apiKey."&format=json";
        $this->db = new Db();
        $this->con = $this->db->connect();
        $this->data = [];
    }

    /**
     * @throws \Exception
     */
    public function getAllRawData(){
        try {
            $request = $this->httpClient->request('GET', $this->apiUrl . "&limit=10000");
            $response = $request->getContent();
            return json_decode($response, true);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function processRawData(): bool
    {
        try {
            $this->data = $this->getAllRawData();

            foreach ($this->data['records'] as $record) {
                $stationId = $this->getOrInsertStation($record);
                $this->insertPollutantReading($stationId, $record);
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;

    }

    public function getData(): array
    {
        return $this->data;
    }
    /**
     * @throws Exception
     */
    private function getOrInsertStation(array $record)
    {
        try {
            // Attempt to insert the station
            $query = "INSERT INTO stations (country, state, city, station, latitude, longitude, inserted_on)
                      VALUES (:country, :state, :city, :station, :latitude, :longitude, NOW())";
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                ':country' => $record['country'],
                ':state' => $record['state'],
                ':city' => $record['city'],
                ':station' => $record['station'],
                ':latitude' => $record['latitude'],
                ':longitude' => $record['longitude']
            ]);
            return $this->con->lastInsertId();
        } catch (PDOException $e) {
            // If insertion fails, retrieve the station ID
            if ($e->getCode() === "23000") { // Unique constraint violation
                $query = "SELECT station_id FROM stations WHERE station = :station";
                $stmt = $this->con->prepare($query);
                $stmt->execute([':station' => $record['station']]);
                $stationId = $stmt->fetchColumn();
                if ($stationId) {
                    return $stationId;
                } else {
                    throw new Exception("Failed to retrieve existing station ID.");
                }
            } else {
                throw $e;
            }
        }
    }

    private function insertPollutantReading(int $stationId, array $record)
    {
        $query = "INSERT INTO pollutant_readings (station_id, pollutant_id, pollutant_min, pollutant_max, pollutant_avg, update_date)
                  VALUES (:station_id, :pollutant_id, :pollutant_min, :pollutant_max, :pollutant_avg, :update_date)";
        $stmt = $this->con->prepare($query);
        try {
            $stmt->execute([
                ':station_id' => $stationId,
                ':pollutant_id' => $record['pollutant_id'],
                ':pollutant_min' => $record['min_value'],
                ':pollutant_max' => $record['max_value'],
                ':pollutant_avg' => $record['avg_value'],
                ':update_date' => date('Y-m-d H:i:s', strtotime($record['last_update']))
            ]);
            return $this->con->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return 0;
            }
        }
        return 0;
    }

    /**
     * Fetch the latest pollutant data for all stations in a given city.
     *
     * @param string $cityName
     * @return array
     * @throws Exception
     */

    /**
     * Fetch pollutant data for all stations in a given city.
     *
     * @param string $cityName The name of the city to fetch data for.
     * @param bool $latest If true, fetch only the latest data; if false, fetch all data.
     * @return array
     * @throws Exception
     */
    public function getDataByCity(string $cityName, bool $latest = true): array
    {
        try {
            if ($latest) {
                // Query for the latest data
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                WHERE 
                    s.city = :city
                    AND r.update_date = (
                        SELECT MAX(update_date)
                        FROM pollutant_readings
                        WHERE station_id = s.station_id
                    )
                ORDER BY 
                    s.station ASC, r.pollutant_id ASC;
            ";
            } else {
                // Query for all historical data
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                WHERE 
                    s.city = :city
                ORDER BY 
                    r.update_date DESC;
            ";
            }
            $stmt = $this->con->prepare($query);
            $stmt->execute([':city' => $cityName]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching data for city : ".$cityName . "<br>" . $e->getMessage());
        }
    }
    /**
     * Fetch pollutant data for all stations in a given state.
     *
     * @param string $stateName The name of the state to fetch data for.
     * @param bool $latest If true, fetch only the latest data; if false, fetch all data.
     * @return array
     * @throws Exception
     */
    public function getDataByState(string $stateName, bool $latest = true): array
    {
        try {
            if ($latest) {
                // Query for the latest data for all stations in the state
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.city, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                WHERE 
                    s.state = :state
                    AND r.update_date = (
                        SELECT MAX(update_date)
                        FROM pollutant_readings
                        WHERE station_id = s.station_id
                    )
                ORDER BY 
                    s.city ASC, s.station ASC, r.pollutant_id ASC;
            ";
            } else {
                // Query for all historical data for all stations in the state
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.city, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                WHERE 
                    s.state = :state
                ORDER BY 
                    r.update_date DESC;
            ";
            }

            $stmt = $this->con->prepare($query);
            $stmt->execute([':state' => $stateName]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Organize data by state -> city -> station
            $result = [];
            foreach ($data as $row) {
                $stationData = [
                    'station_id'    => $row['station_id'],
                    'station'       => $row['station'],
                    'latitude'      => $row['latitude'],
                    'longitude'     => $row['longitude'],
                    'pollutants'    => [
                        'pollutant_id'   => $row['pollutant_id'],
                        'pollutant_min'  => $row['pollutant_min'],
                        'pollutant_max'  => $row['pollutant_max'],
                        'pollutant_avg'  => $row['pollutant_avg'],
                        'update_date'    => $row['update_date']
                    ]
                ];

                // Add station data to the city and then to the state
                $result[$row['city']]['stations'][] = $stationData;
            }

            // Wrap the result in a state key
            return [
                'state' => $stateName,
                'cities' => $result
            ];
        } catch (Exception $e) {
            throw new Exception("Error fetching data for state: " . $e->getMessage());
        }
    }
    /**
     * Fetch pollutant data for all stations in all states and organize by state -> city -> station.
     *
     * @param bool $latest If true, fetch only the latest data; if false, fetch all data.
     * @return array
     * @throws Exception
     */
    public function getAllStatesData(bool $latest = true): array
    {
        try {
            if ($latest) {
                // Query for the latest data for all stations in all states
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.city, 
                    s.state, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                WHERE 
                    r.update_date = (
                        SELECT MAX(update_date)
                        FROM pollutant_readings
                        WHERE station_id = s.station_id
                    )
                ORDER BY 
                    s.state ASC, s.city ASC, s.station ASC, r.pollutant_id ASC;
            ";
            } else {
                // Query for all historical data for all stations in all states
                $query = "
                SELECT 
                    s.station_id, 
                    s.station, 
                    s.city, 
                    s.state, 
                    s.latitude, 
                    s.longitude, 
                    r.pollutant_id, 
                    r.pollutant_min, 
                    r.pollutant_max, 
                    r.pollutant_avg, 
                    r.update_date
                FROM 
                    stations s
                INNER JOIN 
                    pollutant_readings r 
                    ON s.station_id = r.station_id
                ORDER BY 
                    s.state ASC, s.city ASC, s.station ASC, r.update_date DESC;
            ";
            }

            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Organize data by state -> city -> station
            $result = [];
            foreach ($data as $row) {
                $stationData = [
                    'station_id'    => $row['station_id'],
                    'station'       => $row['station'],
                    'latitude'      => $row['latitude'],
                    'longitude'     => $row['longitude'],
                    'pollutants'    => [
                        'pollutant_id'   => $row['pollutant_id'],
                        'pollutant_min'  => $row['pollutant_min'],
                        'pollutant_max'  => $row['pollutant_max'],
                        'pollutant_avg'  => $row['pollutant_avg'],
                        'update_date'    => $row['update_date']
                    ]
                ];

                // Add station data to the city, then to the state
                $result[$row['state']][$row['city']]['stations'][] = $stationData;
            }

            // Return the result organized by state -> city -> station
            return [
                'states' => $result
            ];
        } catch (Exception $e) {
            throw new Exception("Error fetching data for all states: " . $e->getMessage());
        }
    }

    /**
     * Get the nearest city based on the provided latitude and longitude.
     * The function will return the name of the nearest city.
     *
     * @param float $lat Latitude of the user.
     * @param float $lng Longitude of the user.
     * @return string City name of the nearest station.
     * @throws Exception
     */
    public function getCityByLatLng(float $lat, float $lng): string
    {
        try {
            // Correct the Haversine formula syntax in the query
            $haversine = "
            6371 * acos(
                cos(radians(:lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(:lng)) + 
                sin(radians(:lat)) * sin(radians(latitude))
            )";

            // Query to get all stations and their latitude/longitude
            $query = "
            SELECT 
                s.station_id,
                s.station, 
                s.city, 
                s.state, 
                s.latitude, 
                s.longitude, 
                $haversine AS distance
            FROM 
                stations s
            ORDER BY 
                distance ASC
            LIMIT 1;
        ";

            // Prepare and execute the query
            $stmt = $this->con->prepare($query);
            $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
            $stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
            $stmt->execute();
            $nearestStation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nearestStation) {
                // Return only the city name
                return $nearestStation['city'];
            } else {
                throw new Exception("No stations found near the given coordinates.");
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching city data: " . $e->getMessage());
        }
    }
    public function getFilteredData($state = null, $city = null, $station = null, $startDate = null, $endDate = null, $latest = true): array
    {
        $query = "SELECT s.state, s.city, s.station, p.pollutant_id, p.pollutant_min, p.pollutant_max, p.pollutant_avg, p.update_date
                  FROM pollutant_readings p
                  JOIN stations s ON p.station_id = s.station_id
                  WHERE 1";

        // Adding conditions to the query dynamically
        if ($state) {
            $query .= " AND s.state = :state";
        }
        if ($city) {
            $query .= " AND s.city = :city";
        }
        if ($station) {
            $query .= " AND s.station = :station";
        }
        if ($startDate) {
            $query .= " AND p.update_date >= :start_date";
            //$startDate .= " 00:00:00";
        }
        if ($endDate) {
            $query .= " AND p.update_date <= :end_date";
            //$endDate .= " 23:59:59";

        }
        if ($latest) {
            $query .= " AND 
                    p.update_date = (
                        SELECT MAX(update_date)
                        FROM pollutant_readings
                        WHERE station_id = s.station_id
                    )";
        }

        $stmt = $this->con->prepare($query);

        if ($state) {
            $stmt->bindParam(':state', $state);
        }
        if ($city) {
            $stmt->bindParam(':city', $city);
        }
        if ($station) {
            $stmt->bindParam(':station', $station);
        }
        if ($startDate) {
            $stmt->bindParam(':start_date', $startDate);
        }
        if ($endDate) {
            $stmt->bindParam(':end_date', $endDate);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

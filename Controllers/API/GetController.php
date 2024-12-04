<?php
namespace RTAQI\Controllers\API;

use League\Plates\Engine;
use RTAQI\Classes\AQIService;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();
        $aqiService = new AqiService();
        $response->headers->set('Content-Type', 'application/json');

        $requestItem = $request->get("request_item");
        $historicData = $request->get("historic_data") != "required";
        switch ($requestItem) {
            case "city": {

                $cityName = $request->get("city_name");
                $requestDetails = [
                    "request_item" => $requestItem,
                    "city_name" => $cityName,
                    "historic_data" => $historicData
                ];
                try {
                    $response->setContent(json_encode([
                        "status" => "success",
                        "request_details" => $requestDetails,
                        "data" => $aqiService->getDataByCity($cityName, $historicData)

                    ]));
                } catch (\Exception $e) {
                    $response->setContent(json_encode([
                        "status" => "error",
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ],
                        "request_details" => $requestDetails
                    ]));
                }
                break;
            }
            case "state": {
                $stateName = $request->get("state_name");
                $requestDetails = [
                    "request_item" => $requestItem,
                    "state_name" => $stateName,
                    "historic_data" => $historicData
                ];
                try {
                    $response->setContent(json_encode([
                        "status" => "success",
                        "request_details" => $requestDetails,
                        "data" => $aqiService->getDataByState($stateName, $historicData)

                    ]));
                } catch (\Exception $e) {
                    $response->setContent(json_encode([
                        "status" => "error",
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ],
                        "request_details" => $requestDetails
                    ]));
                }
                break;
            }
            case "all": {
                $requestDetails = [
                    "request_item" => $requestItem,
                    "historic_data" => $historicData
                ];
                try {
                    $response->setContent(json_encode([
                        "status" => "success",
                        "request_details" => $requestDetails,
                        "data" => $aqiService->getAllStatesData($historicData)

                    ]));
                } catch (\Exception $e) {
                    $response->setContent(json_encode([
                        "status" => "error",
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ],
                        "request_details" => $requestDetails
                    ]));
                }
                break;
            }
            case "filter" : {
                $state = $request->get("state");
                $city = $request->get("city");
                $station = $request->get("station") ?? null;
                $startDate = $request->get("start_date") ?? null;
                $endDate = $request->get("end_date") ?? null;
                $requestDetails = [
                    "request_item" => $requestItem,
                    "state" => $state,
                    "city" => $city,
                    "station" => $station,
                    "start_date" => $startDate,
                    "end_date" => $endDate,
                    "historic_data" => $historicData
                ];
                try {
                    $response->setCOntent(json_encode([
                        'status' => 'success',
                        'data' => $aqiService->getFilteredData($state, $city, $station, $startDate, $endDate, $historicData),
                        'request_details' => $requestDetails
                    ]));
                } catch (\Exception $e) {
                    $response->setContent(json_encode([
                        "status" => "error",
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ],
                        "request_details" => $requestDetails
                    ]));
                }
                break;
            }
            case "location": {
                $lat = $request->get("lat");
                $lng = $request->get("lng");
                $requestDetails = [
                    "request_item" => $requestItem,
                    "historic_data" => $historicData,
                    "lat" => $lat,
                    "lng" => $lng
                ];
                try {
                    $cityName = $aqiService->getCityByLatLng($lat, $lng);
                    $requestDetails["city_name"] = $cityName;
                    $response->setContent(json_encode([
                        "status" => "success",
                        "request_details" => $requestDetails,
                        "data" => $aqiService->getDataByCity($cityName, $historicData)

                    ]));
                } catch (\Exception $e) {
                    $response->setContent(json_encode([
                        "status" => "error",
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ],
                        "request_details" => $requestDetails
                    ]));
                }
                break;
            }
            default:
                $response->setContent(json_encode([
                    "status" => "error",
                    "message" => [
                        "type" => "danger",
                        "text" => "Invalid request"
                    ]
                ]));
        }
        return $response;
    }
}
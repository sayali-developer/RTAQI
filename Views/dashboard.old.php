<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQI Data Filter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.5/daterangepicker.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <label for="state">State</label>
            <select id="state" class="form-select">
                <!-- Dynamic options -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="city">City</label>
            <select id="city" class="form-select">
                <!-- Dynamic options -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="station">Station</label>
            <select id="station" class="form-select">
                <!-- Dynamic options -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="dateRange">Date Range</label>
            <input type="text" id="dateRange" class="form-control" placeholder="Select Date Range">
        </div>
    </div>

    <table id="aqiTable" class="table table-striped table-bordered mt-4">
        <thead>
        <tr>
            <th>State</th>
            <th>City</th>
            <th>Station</th>
            <th>Pollutant ID</th>
            <th>Min Value</th>
            <th>Max Value</th>
            <th>Avg Value</th>
            <th>Time</th>
        </tr>
        </thead>
        <tbody>
        <!-- Data will be populated here -->
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.5/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        const apiUrl = '/api/getFilteredData';

        // Initialize DataTable
        let table = $('#aqiTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true
        });

        // Fetch filter data on page load
        function fetchFilters() {
            // Dummy data for demonstration; can be replaced with API calls
            $('#state').append('<option value="Bihar">Bihar</option>');
            $('#state').append('<option value="Maharashtra">Maharashtra</option>');
            $('#city').append('<option value="Pune">Pune</option>');
            $('#city').append('<option value="Mumbai">Mumbai</option>');
            $('#station').append('<option value="Station A">Station A</option>');
            $('#station').append('<option value="Station B">Station B</option>');
        }

        // Fetch and update table based on filters
        function fetchData() {
            const state = $('#state').val();
            const city = $('#city').val();
            const station = $('#station').val();
            const dateRange = $('#dateRange').val();
            const [startDate, endDate] = dateRange ? dateRange.split(' - ') : [null, null];

            $.ajax({
                url: apiUrl,
                method: 'GET',
                data: {
                    state: state,
                    city: city,
                    station: station,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    if (response.status === 'success') {
                        table.clear();
                        response.data.forEach(function(row) {
                            table.row.add([
                                row.state,
                                row.city,
                                row.station,
                                row.pollutant_id,
                                row.pollutant_min,
                                row.pollutant_max,
                                row.pollutant_avg,
                                row.update_date
                            ]).draw();
                        });
                    }
                }
            });
        }

        // Trigger table refresh when a filter is changed
        $('#state, #city, #station, #dateRange').change(fetchData);

        // Initialize the date range picker
        $('#dateRange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        // Initialize filters and table data
        fetchFilters();
        fetchData();
    });
</script>

</body>
</html>

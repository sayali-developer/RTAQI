<?php
    $this->layout('layouts/user');

?>

<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard</strong></h1>

    <div class="row">
        <div class="col-xl-12">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?= APP_NAME ?> Data</h5>
                </div>
                <div class="card-body py-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <select onchange="updateData()" id="state" class="form-select">
                                        <option>Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <select onchange="updateData()" id="city" class="form-select">
                                        <option>Select City</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Time</label>
                                    <input type="datetime-local" onchange="updateData()" id="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Time</label>
                                    <input type="datetime-local" onchange="updateData()" id="end_date" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="historical_data" class="form-label">Data</label>
                                    <select id="historical_data" onchange="updateData()"  class="form-select">
                                        <option value="latest">Latest</option>
                                        <option value="required" selected>Latest + Historical</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 ">
                                <div class="mb-3">
                                    <button onclick="updateData()" class="btn btn-primary">Get Data</button>
                                </div>
                            </div>

                            <div class="col-12">
                                <table class="table table-striped table-hover table-bordered" id="RTAQI_TABLE">
                                    <thead>
                                        <tr>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Station</th>
                                            <th>Pollutant</th>
                                            <th>MIN</th>
                                            <th>MAX</th>
                                            <th>AVG</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const table = new DataTable('#RTAQI_TABLE', {
            ajax: {
                url: '/api/get/?request_item=filter',
                method: 'GET',
                data: function (d) {
                    d.state = $('#state').val();
                    d.city = $('#city').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.historic_data = $('#historical_data').val();
                },
            },
            columns: [
                {data: 'state'},          // Maps to 'state' in API response
                {data: 'city'},           // Maps to 'city'
                {data: 'station'},        // Maps to 'station'
                {data: 'pollutant_id'},   // Maps to 'pollutant_id'
                {data: 'pollutant_min'},  // Maps to 'pollutant_min'
                {data: 'pollutant_max'},  // Maps to 'pollutant_max'
                {data: 'pollutant_avg'},  // Maps to 'pollutant_avg'
                {data: 'update_date'},    // Maps to 'update_date'
            ],
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            scrollX: true,
            layout: {
                topStart: {
                    buttons: ['excel', 'pdf', 'print']
                }
            },
            order: [[7, 'desc']] // Default ordering by the 'update_date' column
        });
        $.ajax({
            url: '/api/get/?request_item=filter',
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    populateFilters(response); // Populate filters dynamically
                }
            },
        });

        // Refresh table on filter changes
        $('#state, #city, #start_date, #end_date, #historical_data').change(function () {
            table.ajax.reload();
        });
    });
    function onloadFunction() {
        console.log("Updating Table");
    }
    function populateFilters(apiResponse) {
        const data = apiResponse.data; // Array of records
        const stateSelect = $('#state');
        const citySelect = $('#city');

        // Clear existing options
        stateSelect.empty().append('<option value="">All States</option>');
        citySelect.empty().append('<option value="">All Cities</option>');

        // Extract unique states and cities from the flat array
        const uniqueStates = [...new Set(data.map(item => item.state))];
        const citiesByState = {};

        data.forEach(item => {
            if (!citiesByState[item.state]) {
                citiesByState[item.state] = new Set();
            }
            citiesByState[item.state].add(item.city);
        });

        // Populate State filter
        uniqueStates.forEach(state => {
            stateSelect.append(`<option value="${state}">${state}</option>`);
        });

        // Update City filter dynamically when a State is selected
        stateSelect.change(function () {
            const selectedState = $(this).val();
            citySelect.empty().append('<option value="">All Cities</option>');

            if (selectedState && citiesByState[selectedState]) {
                Array.from(citiesByState[selectedState]).forEach(city => {
                    citySelect.append(`<option value="${city}">${city}</option>`);
                });
            }
        });

        // Trigger a refresh to populate cities when page loads (if needed)
        stateSelect.trigger('change');
    }

    function updateData() {
        onloadFunction();
    }
</script>
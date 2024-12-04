// home.js

document.addEventListener("DOMContentLoaded", function() {
    // Check if geolocation is available in the browser
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // On successful location access
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Fetch pollutant data for the city using the API
            fetchPollutantData(lat, lng);
        }, function(error) {
            alert("Location access denied. Defaulting to Pune City.");
            fetchPollutantData("18.51665483077842", "73.93957875193676");

        });
    } else {
        alert("Geolocation is not supported by this browser. Defaulting to Pune City.");
        fetchPollutantData("18.51665483077842", "73.93957875193676");

    }
});

function fetchPollutantData(lat, lng) {
    // API URL with query parameters for latitude and longitude
    const apiUrl = `/api/get/?request_item=location&lat=${lat}&lng=${lng}`;

    // XHR request to fetch pollutant data
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            // Check if the response is successful and contains data
            if (data.status === "success" && data.data.length > 0) {
                displayPollutantData(data);
            } else {
                alert("No pollutant data available.");
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            alert("Failed to fetch pollutant data.");
        });
}

function displayPollutantData(stations) {
    const container = document.getElementById("widgets-container");

    // Clear previous widgets
    container.innerHTML = "<center><h1 class='bg-success text-white py-3 mb-3'>City : " + stations.request_details.city_name + "</h1></center><br />";

    // Iterate over each station in the response
    stations.data.forEach(stationData => {
        // Create a new widget for each station
        const widget = document.createElement("div");
        widget.classList.add("col-md-4");
        widget.classList.add("mb-4");

        // Populate the widget with pollutant data
        widget.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">${stationData.station}</h4>
                    <p class="card-text">${stationData.update_date}</p>
                </div>
                <div class="card-body">
                    <p><strong>Pollutant:</strong> ${stationData.pollutant_id}</p>
                    <p><strong>Min Value:</strong> ${stationData.pollutant_min}</p>
                    <p><strong>Max Value:</strong> ${stationData.pollutant_max}</p>
                    <p><strong>Avg Value:</strong> ${stationData.pollutant_avg}</p>
                </div>
            </div>
        `;

        // Append the widget to the container
        container.appendChild(widget);
    });
}

function initMap() {

    // Create the map and set a temporary center (will be updated with user's location if available)
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 4,
    });

    // Try to get the user's location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Set the values to the elements with IDs 'lat' and 'lng'
                document.getElementById("lat").value = userLocation.lat;
                document.getElementById("lng").value = userLocation.lng;

                // Center the map to the user's location
                map.setCenter(userLocation);
                map.setZoom(12);

                // Optionally add a marker for the user's location
                new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Location",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                    }
                });
            },
            () => {
                console.warn("Geolocation permission denied or not available. Using default center.");
            }
        );
    } else {
        console.warn("Geolocation is not supported by this browser.");
    }

    function clearInputFields() {
        document.getElementById("snsrid").value = "";
        document.getElementById("username").value = "";
        document.getElementById("lat").value = "";
        document.getElementById("lng").value = "";
        document.getElementById("contact").value = "";
    }

    // Clear input fields on page load
    clearInputFields();

    fetch('http://localhost/proyects/MapaDeSensores/php/getlocations.php')
    .then(response => response.text())
    .then(data => {
        const lines = data.split('\n');
        lines.forEach(line => {
            const [id, nombre, lat, lng, wtrlvl, meassure] = line.split(',');
            let position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            let marker = new google.maps.Marker({
                position: position,
                map: map,
                title: nombre,
            });

            let infoWindow = new google.maps.InfoWindow({
                content: `<div style="color: black;"><strong>${nombre}</strong><br>Nivel de Agua promedio: ${wtrlvl/1000} mts</div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
                document.getElementById("snsrid").value = id;
            });
        });
    })
    .catch(error => console.error('Error fetching coordinates:', error));

    let lastMarker = null;

map.addListener('click', function (event) {
    const clickedLocation = event.latLng;

    // Si existe un último marcador, eliminarlo del mapa
    if (lastMarker) {
        lastMarker.setMap(null);
    }

});

}
google.maps.event.addDomListener(window, 'load', initMap);

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('registrar').addEventListener('click', function(event) {
        event.preventDefault();

        const sensorData = {
            snsrid: document.getElementById('snsrid').value,
            username: document.getElementById('username').value,
            lat: document.getElementById('lat').value,
            lng: document.getElementById('lng').value,
            contact: '+52'+document.getElementById('contact').value,
        };

        const csvData = `snsrid,username,userlat,userlng,usernumber\n${sensorData.snsrid},${sensorData.username},${sensorData.lat},${sensorData.lng},${sensorData.contact}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost/proyects/MapaDeSensores/php/regusers.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Usuario registrado exitosamente');
                } else {
                    alert('Error al registrar el Usuario');
                }
            }
        };

        xhr.send('csv=' + encodeURIComponent(csvData));
    });
});
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

                // Center the map to the user's location
                map.setCenter(userLocation);
                map.setZoom(12);

                // Optionally add a marker for the user's location
                new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Location",
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
        document.getElementById("nombre").value = "";
        document.getElementById("wtrlvl").value = "";
        document.getElementById("lat").value = "";
        document.getElementById("lng").value = "";
    }

    // Clear input fields on page load
    clearInputFields();

    fetch('http://localhost/proyects/MapaDeSensores/php/getlocations.php')
    .then(response => response.text())
    .then(data => {
        const lines = data.split('\n');
        lines.forEach(line => {
            const [lat, lng, wtrlvl, nombre] = line.split(',');
            let position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            let marker = new google.maps.Marker({
                position: position,
                map: map,
                title: nombre,
            });

            let infoWindow = new google.maps.InfoWindow({
                content: `<div><strong>${nombre}</strong><br>Nivel de Agua promedio: ${wtrlvl/1000} mts</div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
                document.getElementById("nombre").value = nombre;
                document.getElementById("wtrlvl").value = wtrlvl/1000 + " mts";
                document.getElementById("lat").value = lat;
                document.getElementById("lng").value = lng;
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

    // Crear un nuevo marcador en la ubicación clicada
    lastMarker = new google.maps.Marker({
        position: clickedLocation,
        map: map,
    });

    lastMarker.addListener('rightclick', function () {
        lastMarker.setMap(null);
        lastMarker = null;
        document.getElementById("nombre").value = "";
        document.getElementById("wtrlvl").value = "";
        document.getElementById("lat").value = "";
        document.getElementById("lng").value = "";
    });

    document.getElementById("nombre").value = "";
    document.getElementById("wtrlvl").value = "";
    document.getElementById("lat").value = clickedLocation.lat();
    document.getElementById("lng").value = clickedLocation.lng();
});

}
google.maps.event.addDomListener(window, 'load', initMap);

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('registrar').addEventListener('click', function(event) {
        event.preventDefault();

        const sensorData = {
            nombre: document.getElementById('nombre').value,
            lat: document.getElementById('lat').value,
            lng: document.getElementById('lng').value,
            wtrlvl: (document.getElementById('wtrlvl').value)*1000
        };

        const csvData = `nombre,lat,lng,wtrlvl\n${sensorData.nombre},${sensorData.lat},${sensorData.lng},${sensorData.wtrlvl}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost/proyects/MapaDeSensores/php/regsensor.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Sensor registrado exitosamente');
                } else {
                    alert('Error al registrar el sensor');
                }
            }
        };

        xhr.send('csv=' + encodeURIComponent(csvData));
    });
});
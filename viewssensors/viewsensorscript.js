const parser = new DOMParser();

// Initialize and add the map
async function initMap() {
    // Create the map and set a temporary center (will be updated with user's location if available)
    const { Map } = await google.maps.importLibrary("maps");

    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary(
        "marker",
      );

      const map = new Map(document.getElementById("map"), {
        zoom: 14,
        mapId: "4504f8b37365c3d0",
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

    fetch('http://localhost/proyects/MapaDeSensores/php/getlocations.php')
    .then(response => response.text())
    .then(data => {
        const lines = data.split('\n');
        lines.forEach(line => {
            const [lat, lng, wtrlvl, nombre] = line.split(',');
            let position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            let marker = new AdvancedMarkerElement({
                position: position,
                map: map,
                title: nombre,
            });

            let infoWindow = new google.maps.InfoWindow({
                content: `<div><strong>${nombre}</strong><br>Lat: ${lat}<br>Lng: ${lng}<br>Nivel de Agua promedio: ${wtrlvl/1000} mts</div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        });
    })
    .catch(error => console.error('Error fetching coordinates:', error));

    let lastMarker = null;

}
// Load the map after the page loads
window.onload = initMap;
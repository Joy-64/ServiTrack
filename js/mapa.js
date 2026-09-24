/*PARA QUE FUNCIONE EL MAPA*/

var map = L.map('map').setView([-32.9468, -60.6393], 13);

var limitesRosario = L.latLngBounds(
    [-33.05, -60.85],
    [-32.85, -60.55]
);

map.setMaxBounds(limitesRosario);
map.options.maxBoundsViscosity = 1;
map.setMinZoom(12.5);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.marker([-32.9468, -60.6393]).addTo(map)
    .bindPopup('Rosario')
    .openPopup();


/*PARA EL GEOLOCALIZADOR*/

var geocoder = L.Control.geocoder({
    defaultMarkGeocode: false
})
    .on('markgeocode', function (e) {
        var latlng = e.geocode.center;

        if (!limitesRosario.contains(latlng)) {
            alert("Buscá una ubicación dentro de la zona de Rosario.");
            return;
        }
        L.circleMarker(latlng, {
            radius: 40,
            color: '#3B82F6',
            fillColor: '#22D3EE',
            fillOpacity: 0.75,
            weight: 3
        }).addTo(map)
            .bindPopup(e.geocode.name.split(',').slice(0, 3).join(','))
            .openPopup();

        map.setView(latlng, 16);
    })
    .addTo(map);


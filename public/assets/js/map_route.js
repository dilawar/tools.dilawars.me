function iconify(iconName, tooltip, size = 24) {
    return `<div title='${tooltip}'>
        <iconify-icon icon=${iconName} width='${size}'>
        </iconify-icon>
    </div>`;
}

var osm = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "© OpenStreetMap",
});

var osmHOT = L.tileLayer(
    "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution:
            "© OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team hosted by OpenStreetMap France",
    }
);

// create map.
var map = L.map("map", {
    center: [13.0678, 77.5754],
    layers: [osm, osmHOT],
    zoom: 15,
});

var baseMaps = {
    OpenSteetMap: osm,
    "OpenStreetMap.HOT": osmHOT,
};

var layerControl = L.control.layers(baseMaps).addTo(map);

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution:
        '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

// Add locate control.
L.control.locate().addTo(map);

// Add clear route button.
let icon = iconify("mdi:clear-circle-outline", "Clear Route");
L.easyButton(icon, function (btn, map) {
    console.info("Clearing route...");
    clearRoute();
}).addTo(map);

icon = iconify("mdi:download", "Download GeoJSON");
L.easyButton(icon, function (btn, map) {
    console.info("Downloading route...");
    route = loadRoute();
    geoJSON = {
        type: "Feature",
        geometry: {
            type: "LineString",
            coordinates: route,
        },
    };

    console.info("GeoJSON file: ", geoJSON);
    var element = document.createElement("a");
    element.setAttribute(
        "href",
        "data:text/plain;charset=utf-8," +
            encodeURIComponent(JSON.stringify(geoJSON))
    );
    element.setAttribute("download", "route.geojson");
    element.style.display = "none";
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}).addTo(map);

// Snap to roads computation using OSRM API.
icon = iconify("streamline-ultimate:trip-road", "Snap to roads");
L.easyButton(icon, async function (btn, map) {
    route = loadRoute();
    console.info("Snapping route to roads...", route);
    coords = route.map((p) => `${p[0]},${p[1]}`).join(";");
    baseUrl = "https://router.project-osrm.org";
    api = `${baseUrl}/match/v1/foot/${coords}?steps=false`;
    console.debug("api endpoint ", api);
    resp = await fetch(api);
    json = resp.json();
    console.log("got response ", json);
}); // .addTo(map);

// A layer to store all markers.
var gMarkers = L.layerGroup().addTo(map);

// event listeners
map.on("click", onMapClick);
drawMap();

function onMapClick(e) {
    const p = [e.latlng.lat, e.latlng.lng];
    addToRoute(p);
}

function drawMap() {
    console.debug("drawing map...");
    gMarkers.clearLayers();

    const route = loadRoute();
    console.debug("Route is ", route);

    // show path.
    L.polyline(route).addTo(gMarkers);

    for (const [i, coord] of route.entries()) {
        // console.debug(">> add marker on map: ", coord);
        // TODO: Make dragable true later.
        const marker = L.marker(coord, { draggable: false });
        // marker.on('popupopen', onMarkerPopupOpen);
        marker.bindPopup(
            "<button onclick='deleteMarker(" +
                i +
                ")' class='marker-delete-button'>Delete</button>"
        );
        if ((i + 1) % 5 === 0) {
            marker.bindTooltip((i + 1).toString(), {
                permanent: true,
                offset: [-15, -30],
                direction: "center",
            });
        }
        gMarkers.addLayer(marker);
    }
}

// Thanks https://stackoverflow.com/a/24342585/1805129
function onMarkerPopupOpen() {
    var tempMarker = this;
    $(".marker-delete-button:visible").click(function () {
        console.log("removing marker");
        gMarkers.removeLayer(tempMarker);
    });
}

function deleteMarker(index) {
    console.log("Deleting marker at index ", index);
    let route = loadRoute();
    route.splice(index, 1);
    storeRoute(route);
    drawMap();
}

function addToRoute(coord) {
    let route = loadRoute();
    console.debug("Adding point ", coord, " to route ", route);
    route.push(coord);
    storeRoute(route);
    drawMap();
}

function clearRoute() {
    console.info("Clearning route");
    storeRoute([]);
    drawMap();
}

function loadRoute() {
    routeStr = sessionStorage.getItem("route");
    var route = [];
    try {
        route = JSON.parse(routeStr);
    } catch (error) {
        console.error("Error parsing route: ", error);
    }

    return route ?? [];
}

function storeRoute(route) {
    console.debug("Saving route ", route);
    sessionStorage.setItem("route", JSON.stringify(route));
}

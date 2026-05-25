/**
 * Creates the Leaflet map for the selected project.
 *
 * The latitude, longitude, title and location are read from the
 * data attributes on the projectMap div in project.php.
 */
function initialiseProjectMap() {
  const mapElement = document.getElementById("projectMap");

  if (!mapElement) {
    return;
  }

  const latitude = parseFloat(mapElement.dataset.latitude);
  const longitude = parseFloat(mapElement.dataset.longitude);

  const projectTitle = mapElement.dataset.title;
  const projectLocation = mapElement.dataset.location;

  const projectLatLng = [latitude, longitude];

  // Create the map and centre it on the selected project.
  const map = L.map("projectMap").setView(projectLatLng, 13);

  // Add the OpenStreetMap tile layer.
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  // Add a marker to the project location.
  const marker = L.marker(projectLatLng).addTo(map);

  // Add a popup to the marker.
  marker.bindPopup(projectTitle + "<br>" + projectLocation);
  marker.openPopup();
}

document.addEventListener("DOMContentLoaded", initialiseProjectMap);

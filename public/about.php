<?php

require_once __DIR__ . '/../src/views/header.php';
require_once __DIR__ . '/../src/helpers/escape.php';

$pageTitle = 'About | Tempest';
?>

<section class="hero">
    <p class="eyebrow">About Tempest</p>
    <p>
        Tempest is a cloud-hosted construction project dashboard for project monitoring,
        location mapping, weather risk assessment and air-quality risk assessment.
    </p>
</section>

<section class="card">
    <h3>Purpose</h3>
    <p>
        The dashboard supports construction planning by combining project data, equipment resources,
        location mapping, weather data, air-quality data, forecast data and historical lookup features.
    </p>
</section>

<section class="card">
    <h3>Technology stack</h3>
    <ul>
        <li>Azure virtual machine hosting</li>
        <li>Apache web server</li>
        <li>PHP backend</li>
        <li>HTML, CSS and JavaScript frontend</li>
        <li>Azure-hosted MySQL database</li>
        <li>Leaflet map library</li>
        <li>OpenStreetMap map tiles</li>
        <li>OpenWeather weather APIs</li>
        <li>OpenWeather Air Pollution API</li>
    </ul>
</section>

<section class="card">
    <h3>Dataset</h3>
    <p>
        Project, resource and project-resource data is based on a construction project dataset
        and imported into the Azure-hosted MySQL database.
    </p>
</section>

<section class="card">
    <h3>External services and libraries</h3>
    <ul>
        <li>Leaflet is used to render interactive maps.</li>
        <li>OpenStreetMap provides the map tile layer.</li>
        <li>OpenWeather provides current weather, forecast and historical weather data.</li>
        <li>OpenWeather Air Pollution API provides current, forecast and historical air-quality data.</li>
    </ul>
</section>

<section class="card">

    <h3>Security</h3>
    <p>
        API keys and database credentials are stored server-side and excluded from version control.
        User input is validated, database queries using user-controlled values use prepared statements,
        and dynamic output is escaped before rendering.
    </p>
</section>

<section class="card">
    <h3>Sustainability</h3>
    <p>
        Tempest avoids heavy frontend frameworks, keeps JavaScript and CSS lightweight, uses server-side
        API requests and runs on a right-sized Azure virtual machine to reduce avoidable compute and
        transfer overhead.
    </p>
</section>

<section class="card">
    <h3>AI assistance</h3>
    <p>
        AI assistance was used to support planning, code review, documentation drafting and troubleshooting.
        The implementation, testing, Azure deployment and final evidence were completed and verified by the developer.
    </p>
</section>

<section class="card">
    <h3>References</h3>
    <ul>
        <li>Azure documentation for virtual machines, networking, role assignments and resource locks.</li>
        <li>OpenWeather API documentation for weather, forecast, historical weather and air-pollution data.</li>
        <li>Leaflet documentation for map rendering.</li>
        <li>OpenStreetMap tile usage and attribution guidance.</li>
        <li>PHP documentation for PDO, input filtering and output escaping.</li>
    </ul>
</section>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
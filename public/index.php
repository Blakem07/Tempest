<?php
$pageTitle = 'Dashboard | Tempest';
require_once __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Construction project dashboard</p>
        <h2>Construction project intelligence for weather and air-quality planning</h2>
        <p>
            Tempest displays construction project data, allocated resources, site locations,
            current weather conditions, air-quality readings, forecast data, historical lookups
            and project-specific risk recommendations.
        </p>
        <p class="status">System status: Azure-hosted PHP dashboard online.</p>
    </div>
</section>

<section class="grid" aria-label="Dashboard capabilities">
    <article class="card">
        <h3>Cloud Architecture</h3>
        <p>
            Hosted on an Azure Ubuntu virtual machine running Apache and PHP, with access controlled
            through Azure networking and security rules.
        </p>
    </article>

    <article class="card">
        <h3>Project Data</h3>
        <p>
            Project records, locations and allocated resources are stored in an Azure-hosted MySQL database.
        </p>
        <p><a href="/project.php">View projects</a></p>
    </article>

    <article class="card">
        <h3>Project Map</h3>
        <p>
            Leaflet and OpenStreetMap display selected project locations using latitude and longitude from the database.
        </p>
    </article>

    <article class="card">
        <h3>Weather Risk</h3>
        <p>
            Server-side OpenWeather requests provide current weather data and recommendations for crane, digger
            and dumper truck operations.
        </p>
    </article>

    <article class="card">
        <h3>Air Quality</h3>
        <p>
            OpenWeather Air Pollution data is used to assess AQI risk for earth-moving equipment.
        </p>
    </article>

    <article class="card">
        <h3>Forecast and History</h3>
        <p>
            Forecast and historical lookup pages support forward planning and review of previous site conditions.
        </p>
        <p>
            <a href="/forecast.php">View forecast</a> |
            <a href="/history.php">View history</a>
        </p>
    </article>
</section>

<section class="card">
    <p class="eyebrow">Operational logic</p>
    <h3>Risk recommendation rules</h3>
    <p>
        Tempest checks wind speed, rainfall intensity and AQI level against the equipment allocated to each project.
        Recommendations are generated server-side and displayed with supporting evidence.
    </p>
</section>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
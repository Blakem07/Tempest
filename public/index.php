<?php
$pageTitle = 'Dashboard | Tempest';
require_once __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Phase 1 foundation</p>
        <h2>Construction project intelligence for weather and air-quality planning</h2>
        <p>
            Tempest will display construction project data, site location,
            current weather conditions, air-quality readings, forecasts,
            historical lookups and risk recommendations.
        </p>
        <p class="status">Phase 1 status: Azure-hosted PHP landing page online.</p>
    </div>
</section>

<section class="grid" aria-label="Planned dashboard capabilities">
    <article class="card">
        <h3>Cloud Architecture</h3>
        <p>Hosted on an Azure Ubuntu virtual machine running Apache and PHP.</p>
    </article>

    <article class="card">
        <h3>Project Data</h3>
        <p>Phase 2 will connect to a cloud database containing projects and equipment resources.</p>
    </article>

    <article class="card">
        <h3>Weather Risk</h3>
        <p>Later phases will use server-side OpenWeather requests for project-specific risk recommendations.</p>
    </article>

    <article class="card">
        <h3>Air Quality</h3>
        <p>Later phases will use OpenWeather Air Pollution data to assess earth-moving work risk.</p>
    </article>

    <article class="card">
        <h3>Map</h3>
        <p>Later phases will use Leaflet and OpenStreetMap to display project locations.</p>
    </article>

    <article class="card">
        <h3>Forecasts</h3>
        <p>Later phases will add forecast data so weather conditions can be checked before site work is planned.</p>
    </article>
</section>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
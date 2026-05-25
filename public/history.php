<?php

require_once __DIR__ . '/../src/services/ProjectService.php';
require_once __DIR__ . '/../src/services/HistoryService.php';
require_once __DIR__ . '/../src/helpers/escape.php';

$pageTitle = 'History | Tempest';

$errorMessage = '';
$weatherHistoryMessage = '';
$airQualityHistoryMessage = '';

$projects = [];
$selectedProject = null;
$historicalWeather = null;
$historicalAirQuality = null;
$selectedDate = '';

try {
    $projects = getAllProjects();

    $projectId = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
    $selectedDate = (string) ($_GET['history_date'] ?? '');

    if ($projectId === false) {
        $errorMessage = 'Invalid project selected.';
    } elseif ($projectId !== null) {
        $selectedProject = getProjectById($projectId);

        if ($selectedProject === null) {
            $errorMessage = 'Project not found.';
        } elseif ($selectedDate !== '') {
            try {
                $validDate = validateHistoryDate($selectedDate);

                try {
                    $historicalWeather = getHistoricalWeather($validDate);
                } catch (Throwable $exception) {
                    $weatherHistoryMessage = 'Historical weather data for this date is unavailable under the current API plan.';
                }

                try {
                    $historicalAirQuality = getHistoricalAirQuality(
                        (float) $selectedProject['latitude'],
                        (float) $selectedProject['longitude'],
                        $validDate
                    );
                } catch (Throwable $exception) {
                    $airQualityHistoryMessage = 'Historical air-quality data for this date is unavailable under the current API plan.';
                }
            } catch (Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }
    }
} catch (Throwable $exception) {
    $errorMessage = 'Historical lookup is currently unavailable.';
}

require_once __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <p class="eyebrow">Historical lookup</p>
    <h2>Weather and air-quality history</h2>
    <p>
        Select a project and date to request historical weather and air-quality data.
        Future dates are rejected and API limitations are shown as controlled messages.
    </p>
</section>

<section class="card">
    <form method="get" action="/history.php">
        <label for="project_id"><strong>Select project</strong></label>
        <select id="project_id" name="project_id" required>
            <option value="">Choose a project</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?= e((string) $project['id']) ?>"
                    <?= isset($selectedProject['id']) && (int) $selectedProject['id'] === (int) $project['id'] ? 'selected' : '' ?>>
                    <?= e($project['title']) ?> - <?= e($project['location_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="history_date"><strong>Select date</strong></label>
        <input
            id="history_date"
            name="history_date"
            type="date"
            value="<?= e($selectedDate) ?>"
            required>

        <button type="submit">View history</button>
    </form>
</section>

<?php if ($errorMessage !== ''): ?>
    <section class="notice error">
        <p><?= e($errorMessage) ?></p>
    </section>
<?php endif; ?>

<?php if ($weatherHistoryMessage !== ''): ?>
    <section class="notice error">
        <p><?= e($weatherHistoryMessage) ?></p>
    </section>
<?php endif; ?>

<?php if ($airQualityHistoryMessage !== ''): ?>
    <section class="notice error">
        <p><?= e($airQualityHistoryMessage) ?></p>
    </section>
<?php endif; ?>

<?php if ($selectedProject !== null && $selectedDate !== ''): ?>
    <section class="card">
        <p class="eyebrow">Selected request</p>
        <h3><?= e($selectedProject['title']) ?></h3>
        <p><?= e($selectedProject['location_name']) ?></p>
        <p><strong>Date:</strong> <?= e($selectedDate) ?></p>
    </section>
<?php endif; ?>

<?php if ($historicalWeather !== null || $historicalAirQuality !== null): ?>
    <section class="grid two-column">
        <article class="card">
            <p class="eyebrow">Historical weather</p>
            <h3><?= e($selectedDate) ?></h3>

            <?php if ($historicalWeather !== null): ?>
                <dl class="details-list">
                    <dt>Temperature</dt>
                    <dd><?= e(number_format((float) $historicalWeather['temperature_celsius'], 1)) ?>°C</dd>

                    <dt>Wind</dt>
                    <dd><?= e(number_format((float) $historicalWeather['wind_mph'], 1)) ?>mph</dd>

                    <dt>Condition</dt>
                    <dd><?= e(ucfirst($historicalWeather['weather_description'])) ?></dd>

                    <dt>Humidity</dt>
                    <dd><?= e((string) $historicalWeather['humidity']) ?>%</dd>

                    <dt>Timestamp</dt>
                    <dd><?= e($historicalWeather['timestamp']) ?></dd>
                </dl>
            <?php else: ?>
                <p>No historical weather data available for this request.</p>
            <?php endif; ?>
        </article>

        <article class="card">
            <p class="eyebrow">Historical air quality</p>
            <h3><?= e($selectedDate) ?></h3>

            <?php if ($historicalAirQuality !== null): ?>
                <dl class="details-list">
                    <dt>AQI</dt>
                    <dd><?= e((string) $historicalAirQuality['aqi']) ?></dd>

                    <dt>AQI label</dt>
                    <dd><?= e($historicalAirQuality['aqi_label']) ?></dd>

                    <dt>PM2.5</dt>
                    <dd><?= e(number_format((float) ($historicalAirQuality['components']['pm2_5'] ?? 0), 2)) ?> μg/m³</dd>

                    <dt>PM10</dt>
                    <dd><?= e(number_format((float) ($historicalAirQuality['components']['pm10'] ?? 0), 2)) ?> μg/m³</dd>

                    <dt>Timestamp</dt>
                    <dd><?= e($historicalAirQuality['timestamp']) ?></dd>
                </dl>
            <?php else: ?>
                <p>No historical air-quality data available for this request.</p>
            <?php endif; ?>
        </article>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
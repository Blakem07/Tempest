<?php

require_once __DIR__ . '/../src/services/ProjectService.php';
require_once __DIR__ . '/../src/services/ForecastService.php';
require_once __DIR__ . '/../src/services/WeatherRiskService.php';
require_once __DIR__ . '/../src/services/AirQualityRiskService.php';
require_once __DIR__ . '/../src/helpers/escape.php';

$pageTitle = '8-Day Forecast | Tempest';

$errorMessage = '';
$forecastErrorMessage = '';
$airQualityForecastErrorMessage = '';

$projects = [];
$selectedProject = null;
$resources = [];
$weatherForecast = [];
$airQualityForecast = [];

try {
    $projects = getAllProjects();

    $projectId = filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);

    if ($projectId === false) {
        $errorMessage = 'Invalid project selected.';
    } elseif ($projectId !== null) {
        $selectedProject = getProjectById($projectId);

        if ($selectedProject === null) {
            $errorMessage = 'Project not found.';
        } else {
            $resources = getProjectResources($projectId);

            try {
                $weatherForecast = getEightDayWeatherForecast(
                    (float) $selectedProject['latitude'],
                    (float) $selectedProject['longitude']
                );
            } catch (Throwable $exception) {
                $forecastErrorMessage = '8-day weather forecast is currently unavailable under the active API access.';
            }

            try {
                $airQualityForecast = getAvailableAirQualityForecast(
                    (float) $selectedProject['latitude'],
                    (float) $selectedProject['longitude']
                );
            } catch (Throwable $exception) {
                $airQualityForecastErrorMessage = 'Air-quality forecast is currently unavailable under the active API access.';
            }
        }
    }
} catch (Throwable $exception) {
    $errorMessage = 'Forecast data is currently unavailable.';
}

require_once __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <p class="eyebrow">Forecast</p>
    <h2>8-day project forecast</h2>
    <p>
        Select a project to view the next 8 days of weather forecast data,
        available air-quality forecast data and daily risk recommendations.
    </p>

</section>

<section class="card">
    <form method="get" action="/forecast.php">
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

        <button type="submit">View forecast</button>
    </form>
</section>

<?php if ($errorMessage !== ''): ?>
    <section class="notice error">
        <p><?= e($errorMessage) ?></p>
    </section>
<?php endif; ?>

<?php if ($selectedProject !== null): ?>
    <section class="card">
        <p class="eyebrow">Selected project</p>
        <h3><?= e($selectedProject['title']) ?></h3>
        <p><?= e($selectedProject['location_name']) ?></p>
    </section>

    <?php if ($forecastErrorMessage !== ''): ?>
        <section class="notice error">
            <p><?= e($forecastErrorMessage) ?></p>
        </section>
    <?php else: ?>
        <section class="card">
            <p class="eyebrow">8-day forecast recommendations</p>
            <h3>Weather and air-quality forecast</h3>

            <?php if ($airQualityForecastErrorMessage !== ''): ?>
                <div class="notice error">
                    <p><?= e($airQualityForecastErrorMessage) ?></p>
                </div>
            <?php else: ?>
                <div class="notice">
                    <p>
                        Forecast ranges depend on the active API plan.
                    </p>
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weather</th>
                        <th>Temperature</th>
                        <th>Wind</th>
                        <th>Weather recommendation</th>
                        <th>AQI</th>
                        <th>AQI recommendation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weatherForecast as $day): ?>
                        <?php
                        $weatherRisk = assessWeatherRisk($day, $resources);
                        $aqiDay = $airQualityForecast[$day['date']] ?? null;
                        $aqiRisk = $aqiDay !== null
                            ? assessAirQualityRisk($aqiDay, $resources)
                            : null;
                        ?>
                        <tr>
                            <td><?= e($day['date']) ?></td>
                            <td><?= e(ucfirst($day['weather_description'])) ?></td>
                            <td>
                                <?= e(number_format((float) $day['temperature_min'], 1)) ?>°C -
                                <?= e(number_format((float) $day['temperature_max'], 1)) ?>°C
                            </td>
                            <td><?= e(number_format((float) $day['wind_mph'], 1)) ?>mph</td>
                            <td>
                                <strong><?= e($weatherRisk['level']) ?></strong><br>
                                <?= e($weatherRisk['message']) ?>
                            </td>
                            <td>
                                <?php if ($aqiDay !== null): ?>
                                    <?= e((string) $aqiDay['aqi']) ?> - <?= e($aqiDay['aqi_label']) ?>
                                <?php else: ?>
                                    Not available
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($aqiRisk !== null): ?>
                                    <strong><?= e($aqiRisk['level']) ?></strong><br>
                                    <?= e($aqiRisk['message']) ?>
                                <?php else: ?>
                                    Air-quality forecast unavailable for this date.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
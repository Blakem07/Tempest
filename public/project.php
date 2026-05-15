<?php

require_once __DIR__ . '/../src/services/ProjectService.php';
require_once __DIR__ . '/../src/helpers/escape.php';
require_once __DIR__ . '/../src/services/WeatherService.php';
require_once __DIR__ . '/../src/services/WeatherRiskService.php';

$pageTitle = 'Projects | Tempest';

$errorMessage = '';
$projects = [];
$selectedProject = null;
$resources = [];

$weather = null;
$weatherRisk = null;
$weatherErrorMessage = '';

// Load project data and optional selected project details.
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
                $weather = getCurrentWeather(
                    (float) $selectedProject['latitude'],
                    (float) $selectedProject['longitude']
                );

                $weatherRisk = assessWeatherRisk($weather, $resources);
            } catch (Throwable $exception) {
                $weatherErrorMessage = 'Current weather data is currently unavailable.';
            }
        }
    }
} catch (Throwable $exception) {
    $errorMessage = 'Project data is currently unavailable.';
}

require_once __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <p class="eyebrow">Project dashboard</p>
    <h2>Construction project data</h2>
    <p>Select a project to view its description, location, allocated equipment resources and project map.</p>
</section>

<section class="card">
    <form method="get" action="/project.php">
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

        <button type="submit">View project</button>
    </form>
</section>

<?php
// Display an error message when project data cannot be loaded.
if ($errorMessage !== ''): ?>
    <section class="notice error">
        <p><?= e($errorMessage) ?></p>
    </section>
<?php endif; ?>

<?php
// Display selected project details and allocated equipment resources.
if ($selectedProject !== null): ?>
    <section class="grid two-column">
        <article class="card">
            <p class="eyebrow">Selected project</p>
            <h3><?= e($selectedProject['title']) ?></h3>
            <p><?= e($selectedProject['description']) ?></p>

            <dl class="details-list">
                <dt>Project manager</dt>
                <dd><?= e($selectedProject['manager']) ?></dd>

                <dt>Location</dt>
                <dd><?= e($selectedProject['location_name']) ?></dd>

                <dt>Latitude</dt>
                <dd><?= e((string) $selectedProject['latitude']) ?></dd>

                <dt>Longitude</dt>
                <dd><?= e((string) $selectedProject['longitude']) ?></dd>
            </dl>
        </article>

        <article class="card">
            <p class="eyebrow">Allocated resources</p>
            <h3>Equipment list</h3>

            <?php if (count($resources) === 0): ?>
                <p>No resources have been allocated to this project.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Type</th>
                            <th>Conditions of use</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $resource): ?>
                            <tr>
                                <td><?= e($resource['name']) ?></td>
                                <td><?= e($resource['resource_type']) ?></td>
                                <td><?= e($resource['conditions_of_use']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
    </section>

    <?php
    // Display selected project map using latitude and longitude from the cloud database.
    ?>
    <section class="card">
        <p class="eyebrow">Project location</p>
        <h3>Site map</h3>
        <p>The map marker uses latitude and longitude retrieved from the cloud database.</p>

        <div
            id="projectMap"
            class="map-panel"
            data-latitude="<?= e((string) $selectedProject['latitude']) ?>"
            data-longitude="<?= e((string) $selectedProject['longitude']) ?>"
            data-title="<?= e($selectedProject['title']) ?>"
            data-location="<?= e($selectedProject['location_name']) ?>"
            aria-label="Map showing selected construction project location">
        </div>

        <p id="mapFallback" class="map-fallback" hidden>
            Map could not be loaded. Project coordinates:
            <?= e((string) $selectedProject['latitude']) ?>,
            <?= e((string) $selectedProject['longitude']) ?>.
        </p>
    </section>

    <section class="card">
        <p class="eyebrow">Current weather</p>
        <h3>Weather risk assessment</h3>

        <?php if ($weatherErrorMessage !== ''): ?>
            <div class="notice error">
                <p><?= e($weatherErrorMessage) ?></p>
            </div>
        <?php elseif ($weather !== null && $weatherRisk !== null): ?>
            <div class="weather-grid">
                <div class="metric">
                    <span class="metric-label">Temperature</span>
                    <strong><?= e(number_format((float) $weather['temperature_celsius'], 1)) ?>°C</strong>
                </div>

                <div class="metric">
                    <span class="metric-label">Wind speed</span>
                    <strong><?= e(number_format((float) $weather['wind_mph'], 1)) ?>mph</strong>
                    <small><?= e(number_format((float) $weather['wind_metres_per_second'], 1)) ?>m/s</small>
                </div>

                <div class="metric">
                    <span class="metric-label">Condition</span>
                    <strong><?= e(ucfirst($weather['weather_description'])) ?></strong>
                </div>

                <div class="metric">
                    <span class="metric-label">Humidity</span>
                    <strong><?= e((string) $weather['humidity']) ?>%</strong>
                </div>

                <div class="metric">
                    <span class="metric-label">Timestamp</span>
                    <strong><?= e($weather['timestamp']) ?></strong>
                </div>
            </div>

            <div class="risk-card risk-<?= e(strtolower($weatherRisk['level'])) ?>">
                <p class="eyebrow">Recommendation</p>
                <h4><?= e($weatherRisk['level']) ?> weather risk</h4>

                <?php foreach ($weatherRisk['messages'] as $message): ?>
                    <p><?= e($message) ?></p>
                <?php endforeach; ?>

                <p><strong>Evidence:</strong></p>
                <ul>
                    <?php foreach ($weatherRisk['evidence'] as $evidenceItem): ?>
                        <li><?= e($evidenceItem) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
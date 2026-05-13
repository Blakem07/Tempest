<?php

require_once __DIR__ . '/../src/services/ProjectService.php';
require_once __DIR__ . '/../src/helpers/escape.php';

$pageTitle = 'Projects | Tempest';

$errorMessage = '';
$projects = [];
$selectedProject = null;
$resources = [];

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
    <p>Select a project to view its description, location and allocated equipment resources from the cloud database.</p>
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
                <dt>Location</dt>
                <dd><?= e($selectedProject['location_name']) ?></dd>

                <dt>Latitude</dt>
                <dd><?= e((string) $selectedProject['latitude']) ?></dd>

                <dt>Longitude</dt>
                <dd><?= e((string) $selectedProject['longitude']) ?></dd>

                <dt>Start date</dt>
                <dd><?= e($selectedProject['start_date']) ?></dd>

                <dt>End date</dt>
                <dd><?= e($selectedProject['end_date']) ?></dd>
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
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $resource): ?>
                            <tr>
                                <td><?= e($resource['name']) ?></td>
                                <td><?= e($resource['resource_type']) ?></td>
                                <td><?= e((string) $resource['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
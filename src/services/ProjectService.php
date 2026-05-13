<?php

require_once __DIR__ . '/../config/database.php';

function getAllProjects(): array
{
    $pdo = databaseConnection();

    $statement = $pdo->query(
        'SELECT id, title, location_name
         FROM projects
         ORDER BY title ASC'
    );

    return $statement->fetchAll();
}

function getProjectById(int $projectId): ?array
{
    $pdo = databaseConnection();

    $statement = $pdo->prepare(
        'SELECT id, title, description, location_name, latitude, longitude, start_date, end_date
         FROM projects
         WHERE id = :id
         LIMIT 1'
    );

    $statement->execute([
        'id' => $projectId,
    ]);

    $project = $statement->fetch();

    return $project ?: null;
}

function getProjectResources(int $projectId): array
{
    $pdo = databaseConnection();

    $statement = $pdo->prepare(
        'SELECT r.name, r.resource_type, pr.quantity
         FROM project_resources pr
         INNER JOIN resources r ON r.id = pr.resource_id
         WHERE pr.project_id = :project_id
         ORDER BY r.name ASC'
    );

    $statement->execute([
        'project_id' => $projectId,
    ]);

    return $statement->fetchAll();
}

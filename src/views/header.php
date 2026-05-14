<?php
$pageTitle = $pageTitle ?? 'Tempest - Construction Project Cloud Dashboard';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Azure-hosted construction project dashboard for project location, weather and air-quality risk monitoring.">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1>Tempest</h1>
            <p class="subtitle">Construction Project Cloud Dashboard</p>

            <nav aria-label="Main navigation">
                <a href="/">Dashboard</a>
                <a href="/project.php">Projects</a>
                <a href="/about.php">About</a>
            </nav>
        </div>
    </header>

    <main class="container">
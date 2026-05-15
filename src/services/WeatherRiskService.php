<?php

// Conservative application threshold informed by CPA crane wind guidance.
const CRANE_WIND_RISK_THRESHOLD_MPH = 20;

/**
 * Assess weather-related construction risk for a selected project.
 *
 * Compares current OpenWeather data against the equipment allocated to the
 * selected project. The function returns a structured recommendation used by
 * the dashboard to display the risk level, user-facing message and supporting
 * evidence.
 *
 * Implemented rules:
 * - If wind speed is greater than 20mph and the project includes a crane,
 *   crane works should not be carried out.
 * - If heavy rain is reported and the project includes earth-moving equipment,
 *   works may be delayed.
 *
 * @param array $weather Current weather data normalised by WeatherService.
 * @param array $resources Resource records allocated to the selected project.
 * @return array Risk summary.
 */
function assessWeatherRisk(array $weather, array $resources): array
{
    // Normalise resource names so matching is case-insensitive.
    $resourceNames = array_map(
        fn(array $resource): string => trim(strtolower((string) ($resource['name'] ?? ''))),
        $resources
    );

    $hasCrane = in_array('crane', $resourceNames, true);
    $hasDigger = in_array('digger', $resourceNames, true);
    $hasDumperTruck = in_array('dumper truck', $resourceNames, true);
    $hasLoader = in_array('loader', $resourceNames, true);

    $windMph = (float) ($weather['wind_mph'] ?? 0);
    $weatherDescription = trim(strtolower((string) ($weather['weather_description'] ?? '')));

    $highRiskRainDescriptions = [
        'heavy intensity rain',
        'very heavy rain',
        'extreme rain',
        'heavy rain',
    ];

    if ($hasCrane && $windMph > CRANE_WIND_RISK_THRESHOLD_MPH) {
        return [
            'level' => 'High',
            'message' => 'Crane works should not be carried out because wind speed exceeds '
                . CRANE_WIND_RISK_THRESHOLD_MPH . 'mph.',
            'evidence' => 'Wind speed: ' . number_format($windMph, 1) . 'mph. Resource: Crane.',
        ];
    }

    // Heavy rain affects projects using earth-moving equipment.
    if (
        in_array($weatherDescription, $highRiskRainDescriptions, true)
        && ($hasDigger || $hasDumperTruck || $hasLoader)
    ) {
        return [
            'level' => 'High',
            'message' => 'Works may be delayed because heavy rainfall affects earth-moving equipment operations.',
            'evidence' => 'Weather condition: ' . $weatherDescription . '. Affected resources include diggers, dumper trucks or loaders.',
        ];
    }

    return [
        'level' => 'Low',
        'message' => 'No weather restriction is currently triggered for the selected project resources.',
        'evidence' => 'No configured wind or heavy-rain resource rule was triggered.',
    ];
}

<?php

// Conservative application threshold informed by CPA crane wind guidance.
const CRANE_WIND_RISK_THRESHOLD_MPH = 20;

/**
 * Assess weather-related construction risk for a selected project.
 *
 * Evaluates every weather rule before returning so multiple warnings can be
 * shown at the same time. This prevents one triggered rule from hiding another
 * triggered rule.
 *
 * Implemented assessment rules:
 * - If wind speed is greater than 20mph and the project includes a crane,
 *   crane work should not be carried out.
 * - If weather description is heavy intensity rain, very heavy rain or extreme
 *   rain, and the project includes both Digger and Dumper Truck, works may be
 *   delayed due to rainfall.
 *
 * @param array $weather Current weather data normalised by WeatherService.
 * @param array $resources Resource records allocated to the selected project.
 * @return array Risk Summary
 */
function assessWeatherRisk(array $weather, array $resources): array
{
    $resourceNames = array_map(
        fn(array $resource): string => trim(strtolower((string) ($resource['name'] ?? ''))),
        $resources
    );

    $hasCrane = in_array('crane', $resourceNames, true);
    $hasDigger = in_array('digger', $resourceNames, true);
    $hasDumperTruck = in_array('dumper truck', $resourceNames, true);

    $windMph = (float) ($weather['wind_mph'] ?? 0);
    $weatherDescription = trim(strtolower((string) ($weather['weather_description'] ?? '')));

    $highRiskRainDescriptions = [
        'heavy intensity rain',
        'very heavy rain',
        'extreme rain',
    ];

    $messages = [];
    $evidence = [];

    if ($hasCrane && $windMph > CRANE_WIND_RISK_THRESHOLD_MPH) {
        $messages[] = 'Crane works should not be carried out because wind speed exceeds '
            . CRANE_WIND_RISK_THRESHOLD_MPH . 'mph.';

        $evidence[] = 'Wind speed: ' . number_format($windMph, 1) . 'mph. Resource: Crane.';
    }

    if (
        in_array($weatherDescription, $highRiskRainDescriptions, true)
        && $hasDigger
        && $hasDumperTruck
    ) {
        $messages[] = 'Works may be delayed due to rainfall because the project includes both digger and dumper truck operations.';

        $evidence[] = 'Weather condition: ' . $weatherDescription
            . '. Resources: Digger and Dumper Truck.';
    }

    if (count($messages) > 0) {
        return [
            'level' => 'High',
            'messages' => $messages,
            'evidence' => $evidence,
            'message' => implode(' ', $messages),
            'evidence_text' => implode(' ', $evidence),
        ];
    }

    return [
        'level' => 'Low',
        'messages' => [
            'No weather restriction is currently triggered for the selected project resources.',
        ],
        'evidence' => [
            'No configured crane wind or heavy-rain digger and dumper truck rule was triggered.',
        ],
        'message' => 'No weather restriction is currently triggered for the selected project resources.',
        'evidence_text' => 'No configured crane wind or heavy-rain digger and dumper truck rule was triggered.',
    ];
}

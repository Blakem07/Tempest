<?php

/**
 * Assess air-quality risk for earth-moving work.
 *
 * Rules:
 * - AQI 1-2: earth-moving work can continue.
 * - AQI 3-5: digger or dumper truck work should not be carried out.
 *
 * @param array $airQuality
 * @param array $resources
 * @return array{
 *     level: string,
 *     messages: array<int, string>,
 *     evidence: array<int, string>,
 *     message: string,
 *     evidence_text: string
 * }
 */
function assessAirQualityRisk(array $airQuality, array $resources): array
{
    $aqi = (int) ($airQuality['aqi'] ?? 0);
    $aqiLabel = (string) ($airQuality['aqi_label'] ?? 'Unknown');

    $resourceNames = array_map(
        fn(array $resource): string => trim(strtolower((string) ($resource['name'] ?? ''))),
        $resources
    );

    $affectedResources = [];

    foreach ($resourceNames as $name) {
        if (str_contains($name, 'digger')) {
            $affectedResources[] = 'digger';
        }

        if (str_contains($name, 'dumper')) {
            $affectedResources[] = 'dumper truck';
        }
    }

    $affectedResources = array_values(array_unique($affectedResources));
    $hasEarthMovingResource = $affectedResources !== [];

    if ($aqi >= 1 && $aqi <= 2) {
        return airQualityRiskResult(
            'Low',
            'Earth-moving work can continue because the current air-quality rating is ' . $aqiLabel . '.',
            'AQI: ' . $aqi . ' (' . $aqiLabel . ').'
        );
    }

    if ($aqi >= 3 && $aqi <= 5) {
        if ($hasEarthMovingResource) {
            return airQualityRiskResult(
                'High',
                'Work involving earth-moving equipment should not be carried out because the current air-quality rating is ' . $aqiLabel . '.',
                'AQI: ' . $aqi . ' (' . $aqiLabel . ') Affected resources: ' . implode(', ', array_map('ucwords', $affectedResources)) . '.'
            );
        }

        return airQualityRiskResult(
            'Medium',
            'Air quality is ' . $aqiLabel . ', but the selected project does not include digger or dumper truck work.',
            'AQI: ' . $aqi . ' (' . $aqiLabel . '). No digger or dumper truck resource is allocated.'
        );
    }

    return airQualityRiskResult(
        'Low',
        'No air-quality restriction is currently triggered for the selected project resources.',
        'No configured AQI rule was triggered.'
    );
}

/**
 * @return array{
 *     level: string,
 *     messages: array<int, string>,
 *     evidence: array<int, string>,
 *     message: string,
 *     evidence_text: string
 * }
 */
function airQualityRiskResult(string $level, string $message, string $evidence): array
{
    return [
        'level' => $level,
        'messages' => [$message],
        'evidence' => [$evidence],
        'message' => $message,
        'evidence_text' => $evidence,
    ];
}

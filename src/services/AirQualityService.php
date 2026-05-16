<?php

require_once __DIR__ . '/../config/env.php';

/**
 * Fetch current air-quality data from the OpenWeather Air Pollution API.
 *
 * @return array{
 *     aqi: int,
 *     aqi_label: string,
 *     components: array<string, float>,
 *     timestamp: string
 * }
 */
function getCurrentAirQuality(float $latitude, float $longitude): array
{
    $apiKey = envValue('OPENWEATHER_API_KEY');

    if (!$apiKey) {
        throw new RuntimeException('OpenWeather API key is missing.');
    }

    $url = 'https://api.openweathermap.org/data/2.5/air_pollution?' . http_build_query([
        'lat' => $latitude,
        'lon' => $longitude,
        'appid' => $apiKey,
    ]);

    $response = file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]));

    if ($response === false) {
        throw new RuntimeException('Air-quality service request failed.');
    }

    $statusCode = getAirQualityHttpStatusCode($http_response_header ?? []);

    if ($statusCode < 200 || $statusCode >= 300) {
        throw new RuntimeException('Air-quality service returned an error.');
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new RuntimeException('Air-quality service returned invalid data.');
    }

    $reading = $data['list'][0] ?? null;

    if (!is_array($reading)) {
        throw new RuntimeException('Air-quality reading is missing.');
    }

    $aqi = (int) ($reading['main']['aqi'] ?? 0);

    if ($aqi < 1 || $aqi > 5) {
        throw new RuntimeException('Air-quality index is invalid.');
    }

    return [
        'aqi' => $aqi,
        'aqi_label' => getAqiLabel($aqi),
        'components' => is_array($reading['components'] ?? null) ? $reading['components'] : [],
        'timestamp' => isset($reading['dt'])
            ? date('Y-m-d H:i:s', (int) $reading['dt'])
            : 'Unavailable',
    ];
}

function getAqiLabel(int $aqi): string
{
    return [
        1 => 'Good',
        2 => 'Fair',
        3 => 'Moderate',
        4 => 'Poor',
        5 => 'Very Poor',
    ][$aqi] ?? 'Unknown';
}

function getAirQualityHttpStatusCode(array $headers): int
{
    return isset($headers[0]) && preg_match('/\s(\d{3})\s/', $headers[0], $matches)
        ? (int) $matches[1]
        : 0;
}

<?php

require_once __DIR__ . '/../config/env.php';

/**
 * Fetch current weather from OpenWeather for the given coordinates.
 *
 * @param float $latitude Project latitude.
 * @param float $longitude Project longitude.
 * @return array Normalised weather data used by the application.
 * @throws RuntimeException If the API key is missing, the request fails,
 *                          or the service returns invalid data.
 */
function getCurrentWeather(float $latitude, float $longitude): array
{
    $apiKey = envValue('OPENWEATHER_API_KEY');

    if (!$apiKey) {
        throw new RuntimeException('OpenWeather API key is missing.');
    }

    $url = 'https://api.openweathermap.org/data/2.5/weather?' . http_build_query([
        'lat' => $latitude,
        'lon' => $longitude,
        'appid' => $apiKey,
        'units' => 'metric',
    ]);

    $context = stream_context_create([
        'http' => [
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        throw new RuntimeException('Weather service request failed.');
    }

    $statusLine = $http_response_header[0] ?? '';

    if (!preg_match('/^HTTP\/\S+\s+200\s/', $statusLine)) {
        throw new RuntimeException('Weather service returned an error.');
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new RuntimeException('Weather service returned invalid data.');
    }

    return normaliseWeatherData($data);
}

/**
 * Convert raw OpenWeather response data into the fields used by the app.
 *
 * @param array $data Raw OpenWeather response data.
 * @return array Normalised weather data.
 */
function normaliseWeatherData(array $data): array
{
    $windMetresPerSecond = (float) ($data['wind']['speed'] ?? 0);

    return [
        'weather_main' => (string) ($data['weather'][0]['main'] ?? 'Unavailable'),

        // Lowercase description makes weather risk matching consistent.
        'weather_description' => strtolower((string) ($data['weather'][0]['description'] ?? 'unavailable')),

        'temperature_celsius' => isset($data['main']['temp'])
            ? (float) $data['main']['temp']
            : null,

        'humidity' => isset($data['main']['humidity'])
            ? (int) $data['main']['humidity']
            : null,

        'wind_metres_per_second' => $windMetresPerSecond,

        // OpenWeather returns wind speed in m/s. Risk rules also need mph.
        'wind_mph' => $windMetresPerSecond * 2.23694,

        'timestamp' => isset($data['dt'])
            ? date('Y-m-d H:i:s', (int) $data['dt'])
            : 'Unavailable',
    ];
}

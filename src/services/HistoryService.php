<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/AirQualityService.php';

/**
 * Validate a historical lookup date.
 */
function validateHistoryDate(string $date): string
{
    $date = trim($date);
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);

    if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
        throw new InvalidArgumentException('Enter a valid date.');
    }

    if ($dateTime > new DateTime('today')) {
        throw new InvalidArgumentException('Historical lookup cannot use a future date.');
    }

    return $date;
}

/**
 * Fetch historical weather using the OpenWeather API.
 *
 * The supplied endpoint uses a city ID rather than project latitude and
 * longitude. 
 *
 * @param string $date Date to fetch historical weather for.
 * @return array{
 *     weather_main: string,
 *     weather_description: string,
 *     temperature_celsius: float|null,
 *     humidity: int|null,
 *     wind_metres_per_second: float,
 *     wind_mph: float,
 *     timestamp: string
 * }
 */
function getHistoricalWeather(string $date): array
{
    $cityId = envValue('OPENWEATHER_HISTORY_CITY_ID');

    if (!$cityId) {
        throw new RuntimeException('OpenWeather historical city ID is missing.');
    }

    $data = fetchHistoricalOpenWeatherData(
        'https://history.openweathermap.org/data/2.5/history/city',
        [
            'id' => $cityId,
            'type' => 'hour',
            'start' => strtotime($date . ' 12:00:00 UTC'),
            'cnt' => 1,
            'units' => 'metric',
        ],
        'Historical weather'
    );

    if (!isset($data['list'][0]) || !is_array($data['list'][0])) {
        throw new RuntimeException('Historical weather returned no data.');
    }

    return normaliseHistoricalWeatherData($data['list'][0]);
}

/**
 * Fetch historical air-quality data by project coordinates.
 *
 * @return array{
 *     aqi: int,
 *     aqi_label: string,
 *     components: array<string, float>,
 *     timestamp: string
 * }
 */
function getHistoricalAirQuality(float $latitude, float $longitude, string $date): array
{
    $data = fetchHistoricalOpenWeatherData(
        'https://api.openweathermap.org/data/2.5/air_pollution/history',
        [
            'lat' => $latitude,
            'lon' => $longitude,
            'start' => strtotime($date . ' 00:00:00 UTC'),
            'end' => strtotime($date . ' 23:59:59 UTC'),
        ],
        'Historical air-quality'
    );

    if (!isset($data['list'][0])) {
        throw new RuntimeException('Historical air-quality returned no data.');
    }

    return normaliseAirQualityData($data);
}

/**
 * Fetch historical data from an OpenWeather endpoint.
 *
 * Handles API key lookup, URL construction, request execution, HTTP status
 * validation, JSON decoding, and response validation.
 *
 * @param array<string, mixed> $params
 * @return array<string, mixed>
 */
function fetchHistoricalOpenWeatherData(string $baseUrl, array $params, string $serviceName): array
{
    $apiKey = envValue('OPENWEATHER_API_KEY');

    if (!$apiKey) {
        throw new RuntimeException('OpenWeather API key is missing.');
    }

    $url = $baseUrl . '?' . http_build_query([
        ...$params,
        'appid' => $apiKey,
    ]);

    $response = file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]));

    if ($response === false) {
        throw new RuntimeException($serviceName . ' request failed.');
    }

    $statusCode = getHistoryHttpStatusCode($http_response_header ?? []);

    if ($statusCode < 200 || $statusCode >= 300) {
        throw new RuntimeException($serviceName . ' data is unavailable under the active API access.');
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new RuntimeException($serviceName . ' returned invalid data.');
    }

    return $data;
}

/**
 * Convert an OpenWeather historical weather row into application fields.
 *
 * @param array $row Raw OpenWeather historical weather row.
 * @return array{
 *     weather_main: string,
 *     weather_description: string,
 *     temperature_celsius: float|null,
 *     humidity: int|null,
 *     wind_metres_per_second: float,
 *     wind_mph: float,
 *     timestamp: string
 * }
 */
function normaliseHistoricalWeatherData(array $row): array
{
    $windMetresPerSecond = (float) ($row['wind']['speed'] ?? 0);

    return [
        'weather_main' => (string) ($row['weather'][0]['main'] ?? 'Unavailable'),
        'weather_description' => strtolower((string) ($row['weather'][0]['description'] ?? 'unavailable')),
        'temperature_celsius' => isset($row['main']['temp']) ? (float) $row['main']['temp'] : null,
        'humidity' => isset($row['main']['humidity']) ? (int) $row['main']['humidity'] : null,
        'wind_metres_per_second' => $windMetresPerSecond,
        'wind_mph' => $windMetresPerSecond * 2.23694,
        'timestamp' => isset($row['dt']) ? date('Y-m-d H:i:s', (int) $row['dt']) : 'Unavailable',
    ];
}

function getHistoryHttpStatusCode(array $headers): int
{
    return isset($headers[0]) && preg_match('/\s(\d{3})\s/', $headers[0], $matches)
        ? (int) $matches[1]
        : 0;
}

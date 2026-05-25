<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/AirQualityService.php';

/**
 * Fetch the next 8 days of weather forecast data.
 *
 * Uses OpenWeather Daily Forecast 16 Days endpoint with cnt=8 to align with
 * the requirement for an 8-day forecast.
 *
 * @return array<int, array{
 *     date: string,
 *     temperature_min: float,
 *     temperature_max: float,
 *     humidity: int,
 *     wind_mph: float,
 *     weather_description: string
 * }>
 */
function getEightDayWeatherForecast(float $latitude, float $longitude): array
{
    $data = fetchForecastData('forecast/daily', $latitude, $longitude, [
        'cnt' => 8,
        'units' => 'metric',
    ], '8-day weather forecast');

    return normaliseEightDayWeatherForecast($data['list']);
}

/**
 * Fetch available air-quality forecast data.
 *
 * OpenWeather Air Pollution forecast usually covers fewer days than the
 * required 8-day weather forecast, so missing AQI days are handled in the UI.
 *
 * @return array<string, array{date: string, aqi: int, aqi_label: string}>
 */
function getAvailableAirQualityForecast(float $latitude, float $longitude): array
{
    $data = fetchForecastData('air_pollution/forecast', $latitude, $longitude, [], 'Air-quality forecast');

    return normaliseAirQualityForecastRowsToDailySummaries($data['list']);
}

/**
 * Fetch forecast data from an OpenWeather endpoint.
 *
 * Handles API key lookup, URL construction, request execution, HTTP status
 * validation, JSON decoding, and response validation.
 *
 * @param array<string, mixed> $extraParams
 * @return array{list: array<int, array<string, mixed>>}
 */
function fetchForecastData(
    string $endpoint,
    float $latitude,
    float $longitude,
    array $extraParams,
    string $serviceName
): array {
    $apiKey = envValue('OPENWEATHER_API_KEY');

    if (!$apiKey) {
        throw new RuntimeException('OpenWeather API key is missing.');
    }

    $url = 'https://api.openweathermap.org/data/2.5/' . $endpoint . '?' . http_build_query([
        'lat' => $latitude,
        'lon' => $longitude,
        'appid' => $apiKey,
        ...$extraParams,
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

    $statusCode = getForecastHttpStatusCode($http_response_header ?? []);

    if ($statusCode < 200 || $statusCode >= 300) {
        throw new RuntimeException($serviceName . ' is unavailable under the active API access.');
    }

    $data = json_decode($response, true);

    if (!is_array($data) || !is_array($data['list'] ?? null)) {
        throw new RuntimeException($serviceName . ' returned invalid data.');
    }

    return $data;
}

/**
 * Convert OpenWeather daily forecast rows into application fields.
 *
 * @param array<int, array<string, mixed>> $rows
 * @return array<int, array{
 *     date: string,
 *     temperature_min: float,
 *     temperature_max: float,
 *     humidity: int,
 *     wind_mph: float,
 *     weather_description: string
 * }>
 */
function normaliseEightDayWeatherForecast(array $rows): array
{
    $forecastRows = [];

    foreach (array_slice($rows, 0, 8) as $row) {
        $windMetresPerSecond = (float) ($row['speed'] ?? $row['wind_speed'] ?? 0);

        $forecastRows[] = [
            'date' => isset($row['dt']) ? date('Y-m-d', (int) $row['dt']) : 'Unavailable',
            'temperature_min' => (float) ($row['temp']['min'] ?? 0),
            'temperature_max' => (float) ($row['temp']['max'] ?? 0),
            'humidity' => (int) ($row['humidity'] ?? 0),
            'wind_mph' => $windMetresPerSecond * 2.23694,
            'weather_description' => strtolower((string) ($row['weather'][0]['description'] ?? 'unavailable')),
        ];
    }

    return $forecastRows;
}

/**
 * Convert OpenWeather hourly air-quality forecast rows into daily worst-AQI summaries.
 *
 * Groups forecast readings by date and keeps the highest valid AQI value for
 * each day.
 *
 * @param array<int, array<string, mixed>> $rows
 * @return array<string, array{date: string, aqi: int, aqi_label: string}>
 */
function normaliseAirQualityForecastRowsToDailySummaries(array $rows): array
{
    $daily = [];

    foreach ($rows as $row) {
        if (!isset($row['dt']) || !isset($row['main']['aqi'])) {
            continue;
        }

        $date = date('Y-m-d', (int) $row['dt']);
        $aqi = (int) $row['main']['aqi'];

        if ($aqi < 1 || $aqi > 5) {
            continue;
        }

        $hasNoAqiForThisDate = !isset($daily[$date]);
        $thisAqiIsWorse = !$hasNoAqiForThisDate && $aqi > $daily[$date]['aqi'];

        if ($hasNoAqiForThisDate || $thisAqiIsWorse) {
            $daily[$date] = [
                'date' => $date,
                'aqi' => $aqi,
                'aqi_label' => getAqiLabel($aqi),
            ];
        }
    }

    return $daily;
}

function getForecastHttpStatusCode(array $headers): int
{
    return isset($headers[0]) && preg_match('/\s(\d{3})\s/', $headers[0], $matches)
        ? (int) $matches[1]
        : 0;
}

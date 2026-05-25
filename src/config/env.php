<?php

/**
 * Loads environment variables from a file.
 *
 * This function reads the contents of a file specified by the `$path` parameter
 * and processes each line to load environment variables into the system.
 *
 * @param string $path The path to the file containing the environment variables.
 * @return void
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue; // skip empty lines and comments
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, ''); // guarantee two parts, key and a value

        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($key !== '') {
            putenv($key . '=' . $value); // make the value available through getenv()
            $_ENV[$key] = $value; // store in php environment array
        }
    }
}

/**
 * Retrieves the value of an environment variable.
 *
 * @param string $key The name of the environment variable.
 * @param string|null $default The default value to return if the environment variable is not set.
 * @return string|null The value of the environment variable, or the default value if the environment variable is not set.
 */
function envValue(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

loadEnv(__DIR__ . '/../../.env');

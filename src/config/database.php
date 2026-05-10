<?php

require_once __DIR__ . '/env.php';

/**
 * Returns the configuration for the database connection.
 *
 * This function retrieves the values of the database connection parameters
 * from the environment variables and returns them as an associative array.
 * If a parameter is not set in the environment variables, it will be set to an empty string.
 *
 * @return array The database connection configuration.
 */
function database_config(): array
{
    return [
        'host' => env_value('DB_HOST', ''),
        'name' => env_value('DB_NAME', ''),
        'user' => env_value('DB_USER', ''),
        'password' => env_value('DB_PASSWORD', ''),
    ];
}

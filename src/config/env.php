<?php

/**
 * Retrieves the value of an environment variable.
 *
 * @param string $key The name of the environment variable.
 * @param string|null $default The default value to return if the environment variable is not set.
 * @return string|null The value of the environment variable, or the default value if the environment variable is not set.
 */
function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

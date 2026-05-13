<?php
// Converts text into safe HTML output so the browser displays it instead of treating it as code.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

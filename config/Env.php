<?php

class Env
{
    public static function load(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $vars = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (!preg_match('/^([A-Z0-9_]+)\s*=\s*(.*)$/', $line, $matches)) {
                continue;
            }

            $value = $matches[2];
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                if (substr($value, -1) === $quote) {
                    $value = substr($value, 1, -1);
                }
            } else {
                $commentPosition = strpos($value, '#');
                if ($commentPosition !== false) {
                    $value = trim(substr($value, 0, $commentPosition));
                }
            }

            $vars[$matches[1]] = $value;
            $_ENV[$matches[1]] = $value;
            putenv($matches[1] . '=' . $value);
        }

        return $vars;
    }
}

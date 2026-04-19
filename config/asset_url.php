<?php

if (!function_exists('smart_asset_version')) {
    function smart_asset_version(): string
    {
        $envVersion = getenv('SMART_ASSET_VERSION');
        if (is_string($envVersion) && trim($envVersion) !== '') {
            return trim($envVersion);
        }

        return '20260419r1';
    }
}

if (!function_exists('smart_send_html_no_cache_headers')) {
    function smart_send_html_no_cache_headers(): void
    {
        if (headers_sent()) {
            return;
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

if (!function_exists('smart_base_path')) {
    function smart_base_path(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('\\', '/', dirname($scriptName));
        $basePath = rtrim($basePath, '/');

        if ($basePath === '' || $basePath === '.') {
            return '';
        }

        return $basePath;
    }
}

if (!function_exists('smart_asset')) {
    function smart_asset(string $path, ?string $version = null): string
    {
        $trimmedPath = trim($path);
        if ($trimmedPath === '') {
            return '';
        }

        if (preg_match('/^(?:https?:)?\/\//i', $trimmedPath) || preg_match('/^(?:data|mailto|tel):/i', $trimmedPath)) {
            return $trimmedPath;
        }

        $cleanPath = ltrim($trimmedPath, '/');
        $basePath = smart_base_path();
        $url = ($basePath !== '' ? $basePath : '') . '/' . $cleanPath;

        if ($version !== null && $version !== '') {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . 'v=' . rawurlencode($version);
        }

        return $url;
    }
}

if (!function_exists('smart_public_fs_path')) {
    function smart_public_fs_path(string $relativePath): string
    {
        $relative = ltrim(str_replace('\\', '/', $relativePath), '/');
        return rtrim(dirname(__DIR__), '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}

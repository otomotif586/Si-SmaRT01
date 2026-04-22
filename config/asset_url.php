<?php

if (!function_exists('smart_asset_version')) {
    function smart_asset_version(): string
    {
        $envVersion = getenv('SMART_ASSET_VERSION');
        if (is_string($envVersion) && trim($envVersion) !== '') {
            return trim($envVersion);
        }

        return '20260419r2';
    }
}

if (!function_exists('smart_asset_file_version')) {
    function smart_asset_file_version(string $path): string
    {
        $cleanPath = ltrim(str_replace('\\', '/', trim($path)), '/');
        if ($cleanPath === '') {
            return smart_asset_version();
        }

        $fsPath = rtrim(dirname(__DIR__), '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);
        if (is_file($fsPath)) {
            $mtime = @filemtime($fsPath);
            if ($mtime !== false) {
                return (string)$mtime;
            }
        }

        return smart_asset_version();
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
        header('Surrogate-Control: no-store');
        header('X-Accel-Expires: 0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

if (!function_exists('smart_is_local_dev')) {
    function smart_is_local_dev(): bool
    {
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $serverName = strtolower((string)($_SERVER['SERVER_NAME'] ?? ''));

        if ($host !== '' && strpos($host, ':') !== false) {
            $host = explode(':', $host, 2)[0];
        }

        $candidates = [$host, $serverName];
        foreach ($candidates as $candidate) {
            if ($candidate === 'localhost' || $candidate === '127.0.0.1' || $candidate === '::1') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('smart_request_nonce')) {
    function smart_request_nonce(): string
    {
        static $nonce = null;
        if ($nonce !== null) {
            return $nonce;
        }

        $nonce = (string)time();
        try {
            $nonce .= '-' . bin2hex(random_bytes(4));
        } catch (Throwable $e) {
            $nonce .= '-' . substr(md5(uniqid('', true)), 0, 8);
        }

        return $nonce;
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

        if ($version === 'auto') {
            $version = smart_asset_file_version($cleanPath);
        }

        if ($version !== null && $version !== '') {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . 'v=' . rawurlencode($version);
        }

        // In local development, always append a per-request nonce to force a full cache bypass.
        if (smart_is_local_dev()) {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . 'dev=' . rawurlencode(smart_request_nonce());
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

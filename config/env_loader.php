<?php
/**
 * Simple Environment Variable Loader
 * Loads variables from .env file into $_ENV
 */

function load_env($file_path = null) {
    if ($file_path === null) {
        $file_path = __DIR__ . '/../.env';
    }
    
    if (!file_exists($file_path)) {
        // If .env doesn't exist, try to use environment variables from server
        return false;
    }
    
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            // Set in $_ENV and putenv
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    return true;
}

// Auto-load when this file is included
load_env();

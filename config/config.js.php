<?php
/**
 * JavaScript Configuration
 * Outputs dynamic configuration based on environment variables
 * Include this file before other JavaScript files to provide global config
 */
require_once __DIR__ . '/env_loader.php';

// Get base URL from environment
$app_base_url = $_ENV['APP_BASE_URL'] ?? getenv('APP_BASE_URL') ?: 'http://localhost:8888/register_sample';

// Ensure trailing slash
$app_base_url = rtrim($app_base_url, '/') . '/';

// Set content type to JavaScript
header('Content-Type: application/javascript');
?>
// Global Application Configuration
window.APP_CONFIG = {
    BASE_URL: '<?php echo addslashes($app_base_url); ?>',
    BASE_PATH: '<?php echo addslashes($app_base_url); ?>',
    ENVIRONMENT: '<?php echo addslashes($_ENV['APP_ENVIRONMENT'] ?? getenv('APP_ENVIRONMENT') ?: 'development'); ?>'
};

// Log configuration for debugging (remove in production)
if (window.APP_CONFIG.ENVIRONMENT === 'development') {
    console.log('App Configuration Loaded:', window.APP_CONFIG);
}

<?php
/**
 * Path utilities for Sistema Gestione Piani di Studio
 * Auto-detects environment (local or production) and configures paths accordingly
 */

// Auto-detect environment
function isLocalDevelopment() {
    $server_name = $_SERVER['SERVER_NAME'];
    return ($server_name == 'localhost' || 
            $server_name == '127.0.0.1' || 
            strpos($server_name, '.local') !== false ||
            strpos($server_name, '.test') !== false);
}

// Nome della cartella del progetto nel server locale (solo per ambiente locale)
// Modifica questo valore in base al nome della tua cartella in XAMPP
define('LOCAL_PROJECT_FOLDER', 'Grafica');

// Define paths based on environment
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

if (isLocalDevelopment()) {
    // Local development environment (XAMPP)
    define('PROJECT_ROOT', DOC_ROOT . '/' . LOCAL_PROJECT_FOLDER);
    define('WEB_ROOT', '/' . LOCAL_PROJECT_FOLDER);
} else {
    // Production environment
    define('PROJECT_ROOT', DOC_ROOT);
    define('WEB_ROOT', '');
}

/**
 * Get the absolute file system path to a resource
 * 
 * @param string $path Path relative to project root
 * @return string Absolute file system path
 */
function getAbsolutePath($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return PROJECT_ROOT . '/' . $path;
}

/**
 * Get a URL path for web resources
 * 
 * @param string $path Path relative to web root
 * @return string URL path
 */
function getUrlPath($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return WEB_ROOT . '/' . $path;
}

/**
 * Get a relative path from the current script to a resource
 * 
 * @param string $path Path relative to project root
 * @return string Relative path from current script
 */
function getRelativePath($path) {
    // Calculate the depth of the current script from project root
    $current_script = $_SERVER['SCRIPT_FILENAME'];
    $relative_dir = str_replace(PROJECT_ROOT, '', dirname($current_script));
    $levels = substr_count($relative_dir, DIRECTORY_SEPARATOR);
    
    // Build the relative path
    $relative_prefix = $levels > 0 ? str_repeat('../', $levels) : '';
    
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    return $relative_prefix . $path;
}

/**
 * Determines the base path prefix based on current page location
 * 
 * @return string Path prefix (like '../' or '../../')
 */
function getBasePathPrefix() {
    // Get the current script path relative to PROJECT_ROOT
    $current_script = $_SERVER['SCRIPT_FILENAME'];
    $relative_path = str_replace(PROJECT_ROOT, '', $current_script);
    
    // Count directory levels
    $levels = substr_count($relative_path, DIRECTORY_SEPARATOR) - 1;
    $levels = max(0, $levels); // Ensure not negative
    
    // Build the prefix
    return $levels > 0 ? str_repeat('../', $levels) : './';
}

/**
 * Debugging function to help with path issues
 * Usage: call debugPaths() anywhere you need to see path information
 */
function debugPaths() {
    echo '<div style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px;">';
    echo '<h3>Path Debugging Info</h3>';
    echo '<pre>';
    echo "Environment: " . (isLocalDevelopment() ? "Local Development" : "Production") . "\n";
    echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
    echo "DOCUMENT_ROOT: " . DOC_ROOT . "\n";
    echo "PROJECT_ROOT: " . PROJECT_ROOT . "\n";
    echo "WEB_ROOT: " . WEB_ROOT . "\n";
    echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
    echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "Current Directory: " . dirname($_SERVER['SCRIPT_FILENAME']) . "\n";
    echo "Relative to Project Root: " . str_replace(PROJECT_ROOT, '', dirname($_SERVER['SCRIPT_FILENAME'])) . "\n";
    echo "Base Path Prefix: " . getBasePathPrefix() . "\n";
    echo '</pre>';
    echo '</div>';
}
<?php
/**
 * Path utilities for Sistema Gestione Piani di Studio
 * Central file to handle all path-related functionality
 */

// Define the document root path
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

// Define the project root path (root of the website)
define('PROJECT_ROOT', DOC_ROOT);

// Define web root for URLs (empty string means site is at web root)
define('WEB_ROOT', '');

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
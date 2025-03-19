<?php
// File: pages/components/shared/breadcrumb.php

// Include the path utilities if not already included
if (!function_exists('getUrlPath')) {
    require_once dirname(dirname(dirname(__DIR__))) . '/config/paths.php';
}

/**
 * Genera un breadcrumb a partire da un array di elementi
 * 
 * @param array $items Array di elementi del breadcrumb nella forma [['text' => 'Testo', 'link' => 'url'], ...]
 */
function generaBreadcrumb($items) {
    echo "<div class='breadcrumb'>";
    echo "<ul>";
    foreach ($items as $item) {
        if (isset($item['link'])) {
            // If link starts with http or /, use it as is, otherwise prepend with URL path
            $url = (preg_match('~^(https?:)?//~', $item['link']) || substr($item['link'], 0, 1) === '/') 
                ? $item['link'] 
                : getUrlPath($item['link']);
            
            echo "<li><a href='" . $url . "'>" . htmlspecialchars($item['text']) . "</a></li>";
        } else {
            echo "<li>" . htmlspecialchars($item['text']) . "</li>";
        }
    }
    echo "</ul>";
    echo "</div>";
}
?>
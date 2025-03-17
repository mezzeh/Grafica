<?php
// File: pages/components/shared/breadcrumb.php

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
            echo "<li><a href='" . $item['link'] . "'>" . htmlspecialchars($item['text']) . "</a></li>";
        } else {
            echo "<li>" . htmlspecialchars($item['text']) . "</li>";
        }
    }
    echo "</ul>";
    echo "</div>";
}
?>
<?php
    $views = scandir(__DIR__);
    array_splice($views, 0, 3);

    foreach ($views as $view) {
        require_once __DIR__ . "/$view";
    }
?>
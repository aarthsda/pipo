<?php
    $ctrls = scandir(__DIR__);
    array_splice($ctrls, 0, 3);

    foreach ($ctrls as $ctrl) {
        require_once __DIR__ . "/$ctrl";
    }
?>
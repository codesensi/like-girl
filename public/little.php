<?php
/*
 * Like Girl — 「点点滴滴」入口
 */
require dirname(__DIR__) . '/app/Controllers/LittleController.php';
(new LittleController())->index();

<?php
/*
 * Like Girl — 「关于」入口
 */
require dirname(__DIR__) . '/app/Controllers/AboutController.php';
(new AboutController())->index();

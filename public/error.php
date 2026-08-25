<?php
/*
 * Like Girl — 「错误/封禁」入口
 */
require dirname(__DIR__) . '/app/Controllers/ErrorController.php';
(new ErrorController())->index();

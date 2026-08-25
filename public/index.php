<?php
/*
 * Like Girl — 前端首页入口（public/ 为文档根）
 */
require dirname(__DIR__) . '/app/Controllers/HomeController.php';
(new HomeController())->index();

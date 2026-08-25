<?php
/*
 * Like Girl — 后台首页入口
 */
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/DashboardController.php';
(new DashboardController())->index();

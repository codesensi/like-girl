<?php
/*
 * Like Girl — 后台「恋爱清单」入口（public/admin/ 为文档根映射）
 */
require_once dirname(__DIR__, 2) . '/app/Admin/Controllers/LoveListController.php';
(new LoveListController())->index();

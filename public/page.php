<?php
/*
 * Like Girl — 「文章详情」入口
 */
require dirname(__DIR__) . '/app/Controllers/PageController.php';
(new PageController())->index();

<?php
/*
 * Like Girl — 「恋爱清单」入口
 */
require dirname(__DIR__) . '/app/Controllers/ListController.php';
(new ListController())->index();

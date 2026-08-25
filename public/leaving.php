<?php
/*
 * Like Girl — 「留言板」入口
 */
require dirname(__DIR__) . '/app/Controllers/LeavingController.php';
(new LeavingController())->index();

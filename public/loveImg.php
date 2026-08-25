<?php
/*
 * Like Girl — 「恋爱相册」入口
 */
require dirname(__DIR__) . '/app/Controllers/LoveImgController.php';
(new LoveImgController())->index();

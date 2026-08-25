<?php
/*
 * Like Girl — 相册分页接口入口（替代原 getPhotos.php）
 * 统一走 app/Controllers/LoveImgController@photos
 */
require dirname(__DIR__) . '/app/Controllers/LoveImgController.php';
(new LoveImgController())->photos();

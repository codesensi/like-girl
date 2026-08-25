<?php
/*
 * Like Girl — 前端首页控制器
 * 渲染 `app/View/pages/index.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';

class HomeController
{
    public function index()
    {
        view('partials/header');
        view('pages/index');
    }
}

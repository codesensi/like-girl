<?php
/*
 * Like Girl — 前端「关于」控制器
 * 渲染 `app/View/pages/about.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Models/About.php';

class AboutController
{
    public function index()
    {
        $about = (new About())->content();
        $about = normalize_asset_paths((array) $about);

        view('partials/header');
        view('pages/about', ['about' => $about]);
    }
}

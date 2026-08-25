<?php
/*
 * Like Girl — 前端「点点滴滴」控制器
 * 渲染 `app/View/pages/little.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Models/Article.php';

class LittleController
{
    public function index()
    {
        $articles = (new Article())->all();
        $articles = normalize_asset_paths((array) $articles);

        view('partials/header');
        view('pages/little', ['articles' => $articles]);
    }
}

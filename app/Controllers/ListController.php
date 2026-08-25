<?php
/*
 * Like Girl — 前端「恋爱清单」控制器
 * 渲染 `app/View/pages/list.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Models/LoveList.php';

class ListController
{
    public function index()
    {
        $listRows = (new LoveList())->all();
        $listRows = normalize_asset_paths((array) $listRows);

        view('partials/header');
        view('pages/list', ['listRows' => $listRows]);
    }
}

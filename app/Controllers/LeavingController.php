<?php
/*
 * Like Girl — 前端「留言板」控制器
 * 渲染 `app/View/pages/leaving.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Models/Leaving.php';

class LeavingController
{
    public function index()
    {
        global $connect;

        // 留言总数
        $nub = "select count(id) as shu from leaving";
        $res = mysqli_query($connect, $nub);
        $leav = mysqli_fetch_array($res);
        $leavCount = $leav ? $leav['shu'] : 0;

        // leavSet 设置（截取条数 / 违禁词）
        $leavSet = "select * from leavSet order by id desc";
        $Set = mysqli_query($connect, $leavSet);
        $Setinfo = $Set ? mysqli_fetch_array($Set) : [];
        $jiequ = isset($Setinfo['jiequ']) ? intval($Setinfo['jiequ']) : 100;

        // 留言列表（最新 $jiequ 条）
        $db = new Database();
        $leavings = $db->fetchAll(
            "SELECT * FROM leaving ORDER BY id DESC LIMIT " . intval($jiequ)
        );
        $leavings = normalize_asset_paths($leavings);

        view('partials/header');
        view('pages/leaving', [
            'leavings'  => $leavings,
            'leavCount' => $leavCount,
            'jiequ'     => $jiequ,
            'Setinfo'   => $Setinfo,
        ]);
    }
}

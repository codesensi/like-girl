<?php
/*
 * Like Girl — 后台「仪表盘」控制器（精简版）
 *
 * 归并原文件：index.php / LG_Info.php / Like_Girl.php / Footer.php
 * 展示后台首页统计卡片与最新留言。
 */

require_once __DIR__ . '/AbstractAdminController.php';
require_once dirname(__DIR__, 2) . '/Core/Database.php';

class DashboardController extends AbstractAdminController
{
    public function index()
    {
        global $connect;

        $db = new Database();

        $counts = [];
        $counts['leaving'] = $this->count('leaving', $connect);
        $counts['article'] = $this->count('article', $connect);
        $counts['lovelist'] = $this->count('lovelist', $connect);
        $counts['loveImg']  = $this->count('loveImg', $connect);

        // 最新 6 条留言
        $latestLeaving = $db->fetchAll("select * from leaving order by id desc limit 0,6");

        // 登录成功提示
        $loginSuccess = false;
        if (!empty($_SESSION['login_success'])) {
            $loginSuccess = true;
            unset($_SESSION['login_success']);
        }

        $this->render('index', [
            'shu'        => $counts['leaving'],
            'diannub'    => $counts['article'],
            'listnub'    => $counts['lovelist'],
            'imgnub'     => $counts['loveImg'],
            'latestLeaving' => $latestLeaving,
            'loginSuccess'  => $loginSuccess,
        ]);
    }

    private function count($table, $connect)
    {
        $row = mysqli_fetch_assoc(mysqli_query($connect, "select count(id) as c from $table"));
        return $row ? $row['c'] : 0;
    }
}

<?php
/*
 * Like Girl — 前端「文章详情」控制器
 * 渲染 `app/View/pages/page.php`。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Models/Article.php';

class PageController
{
    public function index()
    {
        global $connect;

        $id = isset($_GET['id']) ? trim($_GET['id']) : '';

        if (is_numeric($id)) {
            $id = (int) $id;
            $res = mysqli_query($connect, "SELECT * FROM article WHERE id=" . $id . " LIMIT 1");
            $article = $res ? mysqli_fetch_array($res) : null;
            if (!$article) {
                echo "<script>alert('参数错误或页面不存在！');history.back();</script>";
                return;
            }
        } else {
            echo ("<script>alert('参数错误或页面不存在！');history.back();</script>");
            return;
        }

        $article = normalize_asset_paths((array) $article);

        view('partials/header');
        view('pages/page', ['article' => $article]);
    }
}

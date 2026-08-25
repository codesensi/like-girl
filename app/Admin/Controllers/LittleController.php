<?php
/*
 * Like Girl — 后台「点点滴滴」控制器
 *
 * 归并原文件：
 *   littleSet.php    → index
 *   littleAdd.php    → create
 *   littleAddPost.php→ store
 *   modlitt.php      → edit
 *   littleupda.php   → update
 *   dellitt.php      → destroy
 */

require_once __DIR__ . '/AbstractAdminController.php';
require_once dirname(__DIR__, 2) . '/Models/Article.php';

class LittleController extends AbstractAdminController
{
    /* 文章列表 */
    public function index()
    {
        $articles = (new Article())->all();
        $this->render('littleSet', [
            'articles' => $articles,
            'toastr'   => isset($_GET['toastr']) ? $_GET['toastr'] : '',
        ]);
    }

    /* 新增表单 */
    public function create()
    {
        $this->render('littleAdd');
    }

    /* 新增提交（AJAX） */
    public function store()
    {
        global $connect;

        $title = htmlspecialchars(trim($_POST['articletitle'] ?? ''), ENT_QUOTES);
        $text  = trim($_POST['articletext'] ?? '');
        $name  = trim($_POST['articlename'] ?? '');
        $time  = gmdate("Y-m-d", time() + 8 * 3600);

        $charu = "insert into article (articletitle,articletext,articletime,articlename) values ('$title','$text','$time','$name')";
        echo mysqli_query($connect, $charu) ? "1" : "0";
    }

    /* 修改表单 */
    public function edit()
    {
        global $connect;

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $res = mysqli_query($connect, "SELECT * FROM article WHERE id=$id limit 1");
        $mod = $res ? mysqli_fetch_array($res) : [];

        $this->render('modlitt', ['mod' => $mod, 'id' => $id]);
    }

    /* 修改提交（AJAX） */
    public function update()
    {
        global $connect;

        $id    = $_POST['id'] ?? '';
        $title = htmlspecialchars(trim($_POST['articletitle'] ?? ''), ENT_QUOTES);
        $text  = trim($_POST['articletext'] ?? '');

        $sql = "update article set articletitle = '$title', articletext = '$text' where id = '$id'";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 删除（GET id，重定向回列表） */
    public function destroy()
    {
        global $connect;

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (is_numeric($id)) {
            $ok  = mysqli_query($connect, "delete from article where id = $id");
            $msg = $ok ? 'delete_success' : 'delete_fail';
        } else {
            $msg = 'param_error';
        }
        echo "<script>location.href = 'littleSet.php?toastr=$msg';</script>";
    }
}

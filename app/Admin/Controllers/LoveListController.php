<?php
/*
 * Like Girl — 后台「恋爱清单」控制器
 *
 * 归并原文件：
 *   lovelist.php    → index
 *   lovelistAdd.php → create
 *   listaddPost.php → store
 *   modlist.php     → edit
 *   listupda.php    → update
 *   dellist.php     → destroy
 */

require_once __DIR__ . '/AbstractAdminController.php';
require_once dirname(__DIR__, 2) . '/Models/LoveList.php';

class LoveListController extends AbstractAdminController
{
    /* 列表 */
    public function index()
    {
        $listRows = (new LoveList())->all();
        $this->render('lovelist', [
            'listRows' => $listRows,
            'toastr'   => isset($_GET['toastr']) ? $_GET['toastr'] : '',
        ]);
    }

    /* 新增表单 */
    public function create()
    {
        $this->render('lovelistAdd');
    }

    /* 新增提交（AJAX） */
    public function store()
    {
        global $connect;

        $name = htmlspecialchars(trim($_POST['eventname'] ?? ''), ENT_QUOTES);
        $img  = !empty($_POST['img']) ? htmlspecialchars($_POST['img'], ENT_QUOTES) : '';
        $icon = (isset($_POST['icon']) && $_POST['icon'] == 1) ? 1 : 0;

        $sql = "insert into lovelist (eventname,icon,imgurl) values ('$name','$icon','$img')";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 修改表单 */
    public function edit()
    {
        $id     = isset($_GET['id']) ? $_GET['id'] : '';
        $icon   = isset($_GET['icon']) ? $_GET['icon'] : '';
        $name   = isset($_GET['name']) ? $_GET['name'] : '';
        $imgurl = isset($_GET['imgurl']) ? $_GET['imgurl'] : '';

        $this->render('modlist', [
            'id'     => $id,
            'icon'   => $icon,
            'name'   => $name,
            'imgurl' => $imgurl,
        ]);
    }

    /* 修改提交（AJAX） */
    public function update()
    {
        global $connect;

        $name = htmlspecialchars(trim($_POST['eventname'] ?? ''), ENT_QUOTES);
        $id   = isset($_POST['id']) ? trim($_POST['id']) : '';
        $icon = (isset($_POST['icon']) && $_POST['icon']) ? $_POST['icon'] : 0;
        $img  = !empty($_POST['imgurl']) ? htmlspecialchars($_POST['imgurl'], ENT_QUOTES) : '';

        $sql = "update lovelist set eventname = '$name',icon ='$icon',imgurl ='$img' where id ='$id' ";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 删除（GET id，重定向回列表） */
    public function destroy()
    {
        global $connect;

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (is_numeric($id)) {
            $ok = mysqli_query($connect, "delete from lovelist where id = $id");
            $msg = $ok ? 'delete_success' : 'delete_fail';
        } else {
            $msg = 'param_error';
        }
        echo "<script>location.href = '/admin/lovelist.php?toastr=$msg';</script>";
    }
}

<?php
/*
 * Like Girl — 后台「恋爱相册」控制器
 *
 * 归并原文件：
 *   loveImgSet.php → index
 *   loveImgAdd.php → create
 *   ImgAddPost.php → store
 *   modImg.php     → edit
 *   ImgUpdaPost.php→ update
 *   delImg.php     → destroy
 */

require_once __DIR__ . '/AbstractAdminController.php';
require_once dirname(__DIR__, 2) . '/Models/LoveImg.php';

class LoveImgController extends AbstractAdminController
{
    /* 相册列表 */
    public function index()
    {
        $loveImgs = (new LoveImg())->all();
        $this->render('loveImgSet', [
            'loveImgs' => $loveImgs,
            'toastr'   => isset($_GET['toastr']) ? $_GET['toastr'] : '',
        ]);
    }

    /* 新增表单 */
    public function create()
    {
        $inv_date = date("Y-m-d");
        $this->render('loveImgAdd', ['inv_date' => $inv_date]);
    }

    /* 新增提交（AJAX） */
    public function store()
    {
        global $connect;

        $imgText = htmlspecialchars(trim($_POST['imgText'] ?? ''), ENT_QUOTES);
        $imgDatd = trim($_POST['imgDatd'] ?? '');
        $imgUrl  = htmlspecialchars(trim($_POST['imgUrl'] ?? ''), ENT_QUOTES);

        $charu = "insert into loveImg (imgDatd,imgText,imgUrl) values ('$imgDatd','$imgText','$imgUrl')";
        echo mysqli_query($connect, $charu) ? "1" : "0";
    }

    /* 修改表单 */
    public function edit()
    {
        global $connect;

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $res = mysqli_query($connect, "select * from loveImg WHERE id=$id limit 1");
        $Imglist = $res ? mysqli_fetch_array($res) : [];

        $this->render('modImg', ['Imglist' => $Imglist, 'id' => $id]);
    }

    /* 修改提交（AJAX） */
    public function update()
    {
        global $connect;

        $id      = trim($_POST['id'] ?? '');
        $imgText = htmlspecialchars(trim($_POST['imgText'] ?? ''), ENT_QUOTES);
        $imgDatd = trim($_POST['imgDatd'] ?? '');
        $imgUrl  = htmlspecialchars(trim($_POST['imgUrl'] ?? ''), ENT_QUOTES);

        $sql = "update loveImg set imgText = '$imgText', imgDatd = '$imgDatd',imgUrl ='$imgUrl' where id = '$id'";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 删除（GET id，重定向回列表） */
    public function destroy()
    {
        global $connect;

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (is_numeric($id)) {
            $ok  = mysqli_query($connect, "delete from loveImg where id = $id");
            $msg = $ok ? 'delete_success' : 'delete_fail';
        } else {
            $msg = 'param_error';
        }
        echo "<script>location.href = 'loveImgSet.php?toastr=$msg';</script>";
    }
}

<?php
/*
 * Like Girl — 后台「留言板」控制器
 *
 * 归并原文件（后台管理 + 设置）：
 *   leavSet.php → index（留言列表）
 *   leavP.php   → edit（留言前端设置）
 *   leavPPost.php → update（保存设置）
 *   delleav.php   → destroy（删除留言）
 *
 * 说明：leavingPost.php（前台留言提交接口）为公开入口，不经过本控制器鉴权，
 *       由 public/admin/leavingPost.php 独立承载（与原文件行为一致）。
 */

require_once __DIR__ . '/AbstractAdminController.php';
require_once dirname(__DIR__, 2) . '/Core/Database.php';

class LeavingController extends AbstractAdminController
{
    /* 留言列表 */
    public function index()
    {
        global $connect;

        $db   = new Database();
        $rows = $db->fetchAll("SELECT * FROM leaving ORDER BY id DESC");

        $countRes = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM leaving"));
        $count = $countRes ? (int) $countRes['c'] : 0;

        $this->render('leavSet', [
            'leavings'  => $rows,
            'leavCount' => $count,
            'toastr'    => isset($_GET['toastr']) ? $_GET['toastr'] : '',
        ]);
    }

    /* 留言前端设置 */
    public function edit()
    {
        $db      = new Database();
        $Setinfo = $db->fetchOne("SELECT * FROM leavSet ORDER BY id DESC LIMIT 1") ?: [];

        $this->render('leavP', ['Setinfo' => $Setinfo]);
    }

    /* 保存留言设置（AJAX） */
    public function update()
    {
        global $connect;

        $jiequ    = trim($_POST['jiequ'] ?? '');
        $lanjiezf = htmlspecialchars(trim($_POST['lanjiezf'] ?? ''), ENT_QUOTES);

        $sql = "update leavSet set jiequ = '$jiequ',lanjiezf ='$lanjiezf'  ";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 删除留言（GET id，重定向回列表） */
    public function destroy()
    {
        global $connect;

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (is_numeric($id)) {
            $ok  = mysqli_query($connect, "delete from leaving where id = $id");
            $msg = $ok ? 'delete_success' : 'delete_fail';
        } else {
            $msg = 'param_error';
        }
        echo "<script>location.href = 'leavSet.php?toastr=$msg';</script>";
    }
}

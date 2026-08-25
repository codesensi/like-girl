<?php
/*
 * Like Girl — 后台「IP 封禁」控制器
 *
 * 归并原文件：
 *   ipList.php   → index（封禁列表）
 *   ipSet.php    → create（新增表单）
 *   ipAddPost.php→ store（新增封禁）
 *   delip.php    → destroy（删除封禁）
 */

require_once __DIR__ . '/AbstractAdminController.php';

class IpController extends AbstractAdminController
{
    /* 封禁列表 */
    public function index()
    {
        global $connect;

        $res = mysqli_query($connect, "select * from IPerror order by id desc");
        $ipkis = [];
        if ($res) {
            while ($r = mysqli_fetch_array($res)) {
                $ipkis[] = $r;
            }
        }

        $this->render('ipList', [
            'ipkis'  => $ipkis,
            'toastr' => isset($_GET['toastr']) ? $_GET['toastr'] : '',
        ]);
    }

    /* 新增表单 */
    public function create()
    {
        $this->render('ipSet');
    }

    /* 新增封禁（AJAX） */
    public function store()
    {
        global $connect;

        $ip   = trim($_POST['ipdz'] ?? '');
        $bz   = trim($_POST['bz'] ?? '');
        $time = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
        $ipgsd = get_ip_city_New($ip);

        $ipcharu = "insert into IPerror (ipAdd,Time,State,text) values ('$ipgsd','$time','$ip','$bz')";
        echo mysqli_query($connect, $ipcharu) ? "1" : "0";
    }

    /* 删除（GET id，重定向回列表） */
    public function destroy()
    {
        global $connect;

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (is_numeric($id)) {
            $ok  = mysqli_query($connect, "delete from IPerror where id = $id");
            $msg = $ok ? 'delete_success' : 'delete_fail';
        } else {
            $msg = 'param_error';
        }
        echo "<script>location.href = 'ipList.php?toastr=$msg';</script>";
    }
}

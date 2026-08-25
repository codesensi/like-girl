<?php
/*
 * Like Girl — 后台「用户/全局设置」控制器
 *
 * 归并原文件：
 *   user.php    → index（用户/全局设置页）
 *   userPost.php→ update（保存设置）
 */

require_once __DIR__ . '/AbstractAdminController.php';

class UserController extends AbstractAdminController
{
    /* 用户/全局设置页 */
    public function index()
    {
        $this->render('user', [
            'adminuser' => 'admin',
            'adminpw'   => 'love',
        ]);
    }

    /* 保存设置（AJAX） */
    public function update()
    {
        global $connect, $LikeGirl_Code;

        $adminName  = trim($_POST['adminName'] ?? '');
        $pw         = trim($_POST['pw'] ?? '');
        $user       = trim($_POST['userQQ'] ?? '');
        $name       = trim($_POST['userName'] ?? '');
        $Webanimation = trim($_POST['Webanimation'] ?? '');
        $cssCon     = trim($_POST['cssCon'] ?? '');
        $headCon    = htmlspecialchars(trim($_POST['headCon'] ?? ''), ENT_QUOTES);
        $footerCon  = htmlspecialchars(trim($_POST['footerCon'] ?? ''), ENT_QUOTES);
        $SCode      = trim($_POST['SCode'] ?? '');

        if ($LikeGirl_Code == $SCode) {
            $result = mysqli_query(
                $connect,
                "update text set userQQ = '$user',userName = '$name',animation = '$Webanimation' "
            );

            if ($pw) {
                $loginsql = "update login set user = '$adminName' ,pw ='" . md5($pw) . "' where id = 1";
                session_destroy();
            } else {
                $loginsql = "update login set user = '$adminName' where id = 1";
            }
            $loginresult = mysqli_query($connect, $loginsql);
            echo $loginresult ? "1" : "0";

            echo $result ? "3" : "4";

            $diysql = "update diySet set headCon = '$headCon',footerCon = '$footerCon',cssCon = '$cssCon' ";
            $diyresult = mysqli_query($connect, $diysql);
            echo $diyresult ? "5" : "6";
        } else {
            echo "7";
        }
    }
}

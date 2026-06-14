<?php
session_start();
include_once 'connect.php';

$file = $_SERVER['PHP_SELF'];

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $id = $_GET['id'];
    $text = $_GET['text'];
    $QQ = $_GET['QQ'];
    if (is_numeric($id)) {
        $sql = "delete from leaving where id = $id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            echo "<script>location.href = 'leavSet.php?toastr=delete_success';</script>";
        } else {
            echo "<script>location.href = 'leavSet.php?toastr=delete_fail';</script>";
        }
    } else {
        echo "<script>location.href = 'leavSet.php?toastr=param_error';</script>";
    }
} else {
    echo "<script>location.href = 'warning.php?route=$file';</script>";
}

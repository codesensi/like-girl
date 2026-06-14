<?php
session_start();
include_once 'connect.php';

$file = $_SERVER['PHP_SELF'];

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $id = $_GET['id'];
    if (is_numeric($id)) {
        $sql = "delete from article where id = $id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            echo "<script>location.href = 'littleSet.php?toastr=delete_success';</script>";
        } else {
            echo "<script>location.href = 'littleSet.php?toastr=delete_fail';</script>";
        }
    } else {
        echo "<script>location.href = 'littleSet.php?toastr=param_error';</script>";
    }

} else {
    echo "<script>location.href = 'warning.php?route=$file';</script>";
}


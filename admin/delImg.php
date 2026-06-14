<?php
session_start();
include_once 'connect.php';

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $id = $_GET['id'];
    if (is_numeric($id)) {
        $sql = "delete from loveImg where id = $id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            echo "<script>location.href = 'loveImgSet.php?toastr=delete_success';</script>";
        } else {
            echo "<script>location.href = 'loveImgSet.php?toastr=delete_fail';</script>";
        }
    } else {
        echo "<script>location.href = 'loveImgSet.php?toastr=param_error';</script>";
    }

} else {
    echo "<script>location.href = 'warning.php?route=$file';</script>";
}


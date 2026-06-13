<?php
header('Content-Type: application/json');

include_once 'admin/connect.php';
include_once 'admin/Function.php';

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 6;
$offset = ($page - 1) * $limit;

// 查询总数
$totalRes = mysqli_query($connect, "SELECT COUNT(*) as total FROM loveImg");
$totalRow = mysqli_fetch_assoc($totalRes);
$total = $totalRow ? $totalRow['total'] : 0;

// 分页查询
$data = [];
$result = mysqli_query($connect, "SELECT imgUrl, imgDatd, imgText FROM loveImg ORDER BY id DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset));
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'img' => $row['imgUrl'],
            'date' => $row['imgDatd'],
            'text' => $row['imgText']
        ];
    }
}

echo json_encode([
    'code' => 200,
    'data' => $data,
    'total' => $total,
    'page' => $page,
    'limit' => $limit
]);

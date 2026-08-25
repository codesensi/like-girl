<?php
/*
 * Like Girl — 前端「恋爱相册」控制器
 *  - index()  渲染 `app/View/pages/loveImg.php`
 *  - photos() 处理相册分页接口（替代原 getPhotos.php，返回 JSON）
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Models/LoveImg.php';

class LoveImgController
{
    public function index()
    {
        view('partials/header');
        view('pages/loveImg');
    }

    /**
     * 相册分页接口：POST page/limit，返回 { code, data, total, page, limit }。
     */
    public function photos()
    {
        global $connect;

        header('Content-Type: application/json');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 6;
        $offset = ($page - 1) * $limit;

        // 查询总数
        $totalRow = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM loveImg"));
        $total = $totalRow ? $totalRow['total'] : 0;

        // 分页查询
        $data = [];
        $result = mysqli_query(
            $connect,
            "SELECT imgUrl, imgDatd, imgText FROM loveImg ORDER BY id DESC LIMIT "
            . intval($limit) . " OFFSET " . intval($offset)
        );
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
    }
}

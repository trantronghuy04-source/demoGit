<?php
// db_revenue.php

// Nếu cần dùng hàm getOrderById từ db_order.php, bạn có thể require_once nó.


// Hàm chèn dữ liệu doanh thu khi đơn hàng đã delivered
function insertRevenue($order) {
    $db = connect();
    $sql = "INSERT INTO ql_doanhthu (order_id, user_id, tennguoidung, chitietdonhang, tongtiendonhang)
            VALUES (:order_id, :user_id, :tennguoidung, :chitietdonhang, :tongtiendonhang)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order['order_id']);
    $stmt->bindParam(':user_id', $order['user_id']);
    $stmt->bindParam(':tennguoidung', $order['username']);
    $stmt->bindParam(':chitietdonhang', $order['order_details']);
    $stmt->bindParam(':tongtiendonhang', $order['total_price']);
    try {
         $stmt->execute();
         return true;
    } catch(PDOException $e) {
         return "Lỗi khi thêm doanh thu: " . $e->getMessage();
    }
}

// Lấy danh sách doanh thu với các bộ lọc tìm kiếm
function getAllRevenues($search = array()) {
    $db = connect();
    // Lấy ngày mua từ bảng orders thông qua join (trường created_at)
    $sql = "SELECT r.*, o.created_at 
            FROM ql_doanhthu r 
            LEFT JOIN orders o ON r.order_id = o.order_id 
            WHERE 1";
    $params = array();
    // Tìm kiếm theo tên người dùng
    if (!empty($search['tennguoidung'])) {
         $sql .= " AND r.tennguoidung LIKE :tennguoidung";
         $params[':tennguoidung'] = '%' . $search['tennguoidung'] . '%';
    }
    // Tìm kiếm theo chi tiết đơn hàng
    if (!empty($search['chitietdonhang'])) {
         $sql .= " AND r.chitietdonhang LIKE :chitietdonhang";
         $params[':chitietdonhang'] = '%' . $search['chitietdonhang'] . '%';
    }
    // Tìm kiếm theo ngày mua (dựa vào created_at của đơn hàng)
    if (!empty($search['ngaymua_tu']) && !empty($search['ngaymua_den'])) {
         $sql .= " AND o.created_at BETWEEN :ngaymua_tu AND :ngaymua_den";
         $params[':ngaymua_tu'] = $search['ngaymua_tu'];
         $params[':ngaymua_den'] = $search['ngaymua_den'];
    }
    $sql .= " ORDER BY o.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Lấy dữ liệu tổng hợp doanh thu theo nhóm: day, month, year
function getRevenueAggregation($groupBy = 'day') {
    $db = connect();
    $dateFormat = '';
    if ($groupBy == 'day') {
         $dateFormat = '%Y-%m-%d';
    } elseif ($groupBy == 'month') {
         $dateFormat = '%Y-%m';
    } elseif ($groupBy == 'year') {
         $dateFormat = '%Y';
    }
    $sql = "SELECT DATE_FORMAT(o.created_at, '$dateFormat') as period, SUM(r.tongtiendonhang) as total_revenue 
            FROM ql_doanhthu r 
            LEFT JOIN orders o ON r.order_id = o.order_id 
            GROUP BY period 
            ORDER BY period ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
?>

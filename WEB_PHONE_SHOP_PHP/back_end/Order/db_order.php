<?php
// db_order.php

// Thêm mới đơn hàng
function insertOrder($user_id, $username, $email, $phone, $shipping_address, $payment_method, $confirm_bank_transfer, $total_origin, $total_discount, $total_price, $order_details, $qr_code) {
    $db = connect();
    $sql = "INSERT INTO orders 
            (user_id, username, email, phone, shipping_address, payment_method, confirm_bank_transfer, total_origin, total_discount, total_price, order_details, qr_code)
            VALUES (:user_id, :username, :email, :phone, :shipping_address, :payment_method, :confirm_bank_transfer, :total_origin, :total_discount, :total_price, :order_details, :qr_code)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':shipping_address', $shipping_address);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':confirm_bank_transfer', $confirm_bank_transfer);
    $stmt->bindParam(':total_origin', $total_origin);
    $stmt->bindParam(':total_discount', $total_discount);
    $stmt->bindParam(':total_price', $total_price);
    $stmt->bindParam(':order_details', $order_details);
    $stmt->bindParam(':qr_code', $qr_code);
    try {
         $stmt->execute();
         return true;
    } catch(PDOException $e) {
         return "Lỗi khi thêm đơn hàng: " . $e->getMessage();
    }
}

// Lấy danh sách đơn hàng
function getAllOrders() {
    $db = connect();
    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Lấy đơn hàng theo ID
function getOrderById($order_id) {
    $db = connect();
    $sql = "SELECT * FROM orders WHERE order_id = :order_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    return $stmt->fetch();
}

// Cập nhật trạng thái đơn hàng (Hỗ trợ các trạng thái: pending, confirmed, cancelled, delivered)
function updateOrder($order_id, $status) {
    $db = connect();
    $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);
    try {
         $stmt->execute();
         return true;
    } catch(PDOException $e) {
         return "Lỗi khi cập nhật đơn hàng: " . $e->getMessage();
    }
}

// Xóa đơn hàng
function deleteOrder($order_id) {
    $db = connect();
    $sql = "DELETE FROM orders WHERE order_id = :order_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    try {
         $stmt->execute();
         return true;
    } catch(PDOException $e) {
         return "Lỗi khi xóa đơn hàng: " . $e->getMessage();
    }
}
?>

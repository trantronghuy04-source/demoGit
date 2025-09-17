<?php
require_once "db_order.php";
if(isset($_GET['id'])){
    $order_id = $_GET['id'];
    $result = deleteOrder($order_id);
    if($result === true){
         header("Location: Manager.php?action=Order");
         exit();
    } else {
         echo $result;
    }
} else {
    echo "Không có ID để xóa đơn hàng.";
}
?>

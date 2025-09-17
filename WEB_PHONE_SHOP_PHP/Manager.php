<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quản trị</title>
    <link href="public/images/style1.css" rel="stylesheet" />
</head>

<body>
    <?php 
        require_once "DB.php";
    ?>
    <div class="main">
        <div class="header">
            <!-- <div class="header_1">
                <img src="public/images/anh2.jpg" width=" 100" />
                <?php
			        if(isset($_SESSION["username"])){
				        echo "Xin chao ".$_SESSION["username"];
			        }
			        //dem luot nguoi truy cap
			        if(isset($_SESSION["counter"])){
				        $_SESSION["counter"] +=1;
			        }
			        else {$_SESSION["counter"]=1;
			        }
			    echo " So luot truy cap ".$_SESSION["counter"];
			    ?>
            </div> -->
            <div class="header2"><?php require_once "pages/menu_backend.php";?></div>
        </div>
        <div class="content">
            <?php 
            if(isset($_GET["action"])){
               $action= $_GET["action"];
               switch ($action){
                //Quản lý loài sản phẩm 
                case "AdProductType":
                    require_once "back_end/AdProductType/AdProductTypeView.php";
                    break;
                case "UpdateAdProductType":
                    require_once "back_end/AdProductType/AdProductType_update.php";
                    break;
                case "DeleteAdProductType":
                    if (isset($_GET['id'])) {
                        require_once "back_end/AdProductType/db_AdProductType.php";
                        $id = $_GET['id'];
                        deleteProductType($id);
                        header("Location: Manager.php?action=AdProductType");
                        exit();
                    } else {
                        echo "Không có ID để xóa sản phẩm.";
                    }
                    break;
                //Quản lý tin tức
                case "AdNews":
                    require_once "back_end/AdNew/AdNewsView.php";
                    break;
                case "UpdateAdNews":
                    require_once "back_end/AdNew/AdNews_update.php";
                    break;
                    case "DeleteAdNews":
                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            require_once "back_end/AdNew/db_AdNews.php";
                            deleteNews($id); // Gọi hàm xóa tin tức
                            header("Location: Manager.php?action=AdNews"); // Quay lại trang danh sách
                            exit();
                        }
                        break;   
                //Quản lý nhà cung cấp
                case "AdNcc":
                    require_once "back_end/AdNcc/AdNccView.php";
                    break;   
                case "UpdateAdNcc":
                    if (isset($_GET['id'])) {
                        require_once "back_end/AdNcc/AdNcc_Update.php";
                    }
                    break;    
                case "DeleteAdNcc":
                    if (isset($_GET['id'])) {
                        require_once "back_end/AdNcc/db_AdNccView.php";
                        $id = $_GET['id'];
                        deleteNccType($id);
                        header("Location: Manager.php?action=AdNcc");
                        exit();
                    } else {
                        echo "Không có ID để xóa nhà cung cấp.";
                    }
                    break;
                //Quản lý người dùng
                case "QUser":
                    require_once "./back_end/qluser/quserView.php";
                    break;
                case "UpdateQUser":     
                        require_once "./back_end/qluser/quser_update.php";
                    break;
                case "DeleteQUser":
                    if (isset($_GET['id'])) {
                        require_once "./back_end/qluser/db_quser.php";
                        deleteUser($_GET['id']);
                        header("Location: Manager.php?action=QUser");
                        exit();
                    }
                    break;
                //Quản lý Sản phẩm 
                case "AdProduct_add";
                    require_once("back_end/AdProduct/AdProduct_add.php");
                    break;
                case "UpdateAddProduct1":
                    require_once ("back_end/AdProduct/AdProduct_Update.php");
                    break;
                case "AdProduct":
                    echo '<div class="content-container">';
                    require_once "back_end/AdProduct/AdProductView.php";
                    echo '</div>';
                    break;
                //Quản lý khuyến mại
                case 'AdKm': // Hiển thị danh sách khuyến mãi
                    require_once "back_end/km/AdKmView.php";
                    break;

                case 'AddAdKm': // Thêm khuyến mãi mới
                    require_once "back_end/km/AdKm_Up.php";
                    break;

                case 'UpdateAdKm': // Cập nhật thông tin khuyến mãi
                    require_once "back_end/km/AdKm_Up.php";
                    break;
                case 'DeleteAdKm': // Xóa khuyến mãi
                    if (isset($_GET['id'])) {
                        require_once "back_end/km/db_AdKmView.php";
                        deleteKhuyenMai($_GET['id']);
                        header("Location: Manager.php?action=AdKm");
                        exit();
                    }
                    break;
                //View Quản lý kho
                case "qlkho":
                    require_once "back_end/qlkho/KhoView.php";
                    break;
                //View Quản lý doanh thu
                case "qldoanhthu":
                    require_once "back_end/qlkho/DoanhThuView.php";
                    break;
                // Quản lý giỏ hàng
                case "Cart":
                    require_once "back_end/Cart/CartView.php";
                    break;
                case "UpdateCart":
                    require_once "back_end/Cart/Cart_update.php";
                    break;
                case "DeleteCart":
                    if (isset($_GET['id'])) {
                        require_once "back_end/Cart/db_cart.php";
                        $id = $_GET['id'];
                        $result = deleteCart($id);
                        if($result === true){
                            header("Location: Manager.php?action=Cart");
                            exit();
                        } else {
                            echo $result;
                        }
                    } else {
                        echo "Không có ID để xóa giỏ hàng.";
                    }
                    break;
                //Quản lý đơn hàng
                case "Order":
                    require_once "back_end/Order/OrderView.php";
                    break;
                case "UpdateOrder":
                        require_once "back_end/Order/Order_update.php";
                        break;
                case "DeleteOrder":
                        require_once "back_end/Order/Order_delete.php";
                        break;  
                        case "ConfirmOrder":
                            // Lấy thông tin đơn hàng cần xác nhận
                            $order_id = $_POST['order_id'] ?? '';
                            $status = $_POST['status'] ?? 'confirmed';
                            // Thực hiện cập nhật trạng thái (và cập nhật tồn kho nếu cần)
                            require_once "back_end/Order/db_order.php";
                            $result = updateOrder($order_id, $status);
                            if($result === true){
                                // Chuyển hướng lại trang quản lý đơn hàng
                                header("Location: Manager.php?action=Order");
                                exit();
                            } else {
                                echo "Lỗi: " . $result;
                            }
                            break;
                    //Quản lý doanh thu
                case "Revenue":
                    require_once "back_end/Revenue/RevenueView.php";
                    break;
                //Quản lý phản hồi 
                case "News":
                    require_once "back_end/News/NewsView.php";
                    break;
                case "UpdateNews":
                    require_once "back_end/News/News_update.php";
                    break;
                case "DeleteNews":
                    if (isset($_GET['id'])) {
                        require_once "back_end/News/db_news.php";
                        $id = $_GET['id'];
                        $result = deleteNews($id);
                        if($result === true){
                            header("Location: Manager.php?action=News");
                            exit();
                        } else {
                            echo $result;
                        }
                    } else {
                        echo "Không có ID để xóa tin tức.";
                    }
                    break;
                               
                
                    /*case "QLdonhang":
                        require_once "./back_end/QLdonhang/QLdonhangView.php";
                        break;
                    case "UpdateQLdonhang":
                        require_once "./back_end/QLdonhang/db_QLdonhang.php";
                        if (isset($_POST['madonhang'])) {
                            try {
                                $madonhangs = explode(', ', $_POST['madonhang']); // Tách danh sách mã đơn hàng
                                foreach ($madonhangs as $madonhang) {
                                    updateOrderStatus(trim($madonhang), 'Đã xác nhận'); // Xác nhận từng đơn hàng
                                }
                                header("Location: Manager.php?action=QLdonhang&message=confirmed"); // Quay lại trang quản lý đơn hàng
                                exit();
                            } catch (PDOException $e) {
                                echo "Lỗi khi xác nhận đơn hàng: " . $e->getMessage();
                            }
                        }
                        break;
                    case "DeleteQLdonhang":
                        require_once "./back_end/QLdonhang/db_QLdonhang.php";
                        if (isset($_POST['madonhang'])) {
                            try {
                                $madonhangs = explode(', ', $_POST['madonhang']); // Tách danh sách mã đơn hàng
                                foreach ($madonhangs as $madonhang) {
                                    deleteOrder(trim($madonhang)); // Xóa từng đơn hàng
                                }
                                header("Location: Manager.php?action=QLdonhang&message=deleted"); // Quay lại trang quản lý đơn hàng
                                exit();
                            } catch (PDOException $e) {
                                echo "Lỗi khi xóa đơn hàng: " . $e->getMessage();
                            }
                        }
                        break;
                    case "QLdoanhthu":
                        require_once "back_end/qlkho/qlyDoanhThuView.php";
                        break;      
                        case "QLfeedback":
                            require_once "back_end/qlkho/qlyFeedback.php";
                            break;   
                    case "AdNews":
                        require_once "back_end/AdNew/AdNewsView.php";
                        break;
                    case "UpdateAdNews":
                        require_once "back_end/AdNew/AdNews_update.php";
                        break;
                        case "DeleteAdNews":
                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                require_once "back_end/AdNew/db_AdNews.php";
                                deleteNews($id); // Gọi hàm xóa tin tức
                                header("Location: Manager.php?action=AdNews"); // Quay lại trang danh sách
                                exit();
                            }
                            break;*/
                        
                                                   
                }
            }
            ?>
        </div>
        <div class="footer">
            @copyright by NguyenDungxTranHuy
        </div>
    </div>
</body>

</html>
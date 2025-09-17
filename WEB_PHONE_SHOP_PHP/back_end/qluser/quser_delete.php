<?php
require_once "db_quser.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $result = deleteUser($user_id);
    if ($result === true) {
        header("Location: Manager.php?action=QUser&message=deleted");
        exit();
    } else {
        echo $result;
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>

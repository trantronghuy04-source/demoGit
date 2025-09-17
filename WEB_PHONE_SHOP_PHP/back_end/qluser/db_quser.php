<?php
require_once "DB.php";

function getAllUsers() {
    $conn = connect();
    $sql = "SELECT * FROM users ORDER BY user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertUser($username, $email, $phone, $address, $password) {
    $conn = connect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, phone, address, password)
            VALUES (:username, :email, :phone, :address, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':password', $hashed_password);
    try {
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function updateUser($user_id, $username, $email, $phone, $address, $password = null) {
    $conn = connect();
    try {
        if(!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = :username, email = :email, phone = :phone, address = :address, password = :password
                    WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $hashed_password);
        } else {
            $sql = "UPDATE users SET username = :username, email = :email, phone = :phone, address = :address
                    WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
        }
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function deleteUser($user_id) {
    $conn = connect();
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function getUserById($user_id) {
    $conn = connect();
    $sql = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

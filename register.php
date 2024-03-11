<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $pass = $_POST['password'];
     $confirmPassword = $_POST['confirm'];

    if ($pass === $confirmPassword) {
        $password=password_hash($pass,PASSWORD_DEFAULT);
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=watermark_db", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE `username` = ?");
            $stmtCheck->execute([$username]);
            $existingUser = $stmtCheck->fetch();
    
            if ($existingUser) {
                echo json_encode([ 'User already exists']);
                die;
            }else{

            $stmt = $pdo->prepare("INSERT INTO  users (`username`,`password`)VALUES(?,?)");
            $stmt->execute([$username,$password]);
            $_SESSION['user']=$username;
            echo json_encode(['success'=>true]);
            }
        } catch (PDOException $e) {
            echo json_encode(['Error: ' . $e->getMessage()]);
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>

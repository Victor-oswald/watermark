<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userPassword = trim($_POST['password']);
    $username = trim($_POST['username']);


    if ($userPassword !== "" && $username !== "") {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=watermark_db", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT `password` FROM users WHERE `username` = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch();
 
            if($row){
                $hashedPassword = $row['password'];
     

                $access = password_verify($userPassword, $hashedPassword);

                if ($access === true) {
                    $_SESSION['user']=$username;
                    echo json_encode(["success"=>true]);
                }else{
                    echo json_encode(["Incorrect Password"]);
                }
            } else {
                echo json_encode(['The requested account was not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['Please fill the required fields']);
    }
} else {
    echo json_encode('Invalid request');
}
?>

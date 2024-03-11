<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['user'])) {
        echo json_encode(['status' => 'success', 'message' => 'User session is set.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User session is not set.']);
    }
}
    ?>
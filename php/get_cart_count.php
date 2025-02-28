<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

$serverIP = $_ENV['SERVER_IP'] ?? '127.0.0.1';


header("Access-Control-Allow-Origin: http://$serverIP");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

session_start();
require_once '../db.php';

header('Content-Type: application/json');

try {
    $pdo = getReadConnection();
    
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }

    $currentUser = isset($_SESSION['user_id']);
    $cartItemCount = 0;

    if ($currentUser) {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(quantity), 0) AS total_items
            FROM cart_items
            JOIN carts ON cart_items.cart_id = carts.cart_id
            WHERE carts.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartItemCount = (int)$result['total_items'];
    }

    echo json_encode([
        'success' => true,
        'count' => $cartItemCount,
        'user_logged_in' => $currentUser
    ]);

} catch (Exception $e) {
    error_log("Cart count error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch cart count',
        'error' => $e->getMessage()
    ]);
}

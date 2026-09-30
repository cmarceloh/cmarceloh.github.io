<?php
/*
 * contacto.php — Generado por Landing Page Builder
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$recipient_email = 'cmarcelohernandez@gmail.com';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Soporte para application/json y multipart/form-data
$input = json_decode(file_get_contents('php://input'), true);
if (is_array($input)) {
    $nombre   = isset($input['nombre'])   ? sanitize($input['nombre'])   : (isset($input['name']) ? sanitize($input['name']) : '');
    $email    = isset($input['email'])    ? sanitize($input['email'])    : '';
    $telefono = isset($input['telefono']) ? sanitize($input['telefono']) : (isset($input['phone']) ? sanitize($input['phone']) : '');
    $mensaje  = isset($input['mensaje'])  ? sanitize($input['mensaje'])  : (isset($input['message']) ? sanitize($input['message']) : '');
} else {
    $nombre   = isset($_POST['nombre'])   ? sanitize($_POST['nombre'])   : '';
    $email    = isset($_POST['email'])    ? sanitize($_POST['email'])    : '';
    $telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : '';
    $mensaje  = isset($_POST['mensaje'])  ? sanitize($_POST['mensaje'])  : '';
}

if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido.']);
    exit;
}

$subject = "Nuevo mensaje de contacto: " . $nombre;
$body  = "Has recibido un nuevo mensaje de contacto:\n\n";
$body .= "Nombre:    " . $nombre . "\n";
$body .= "Email:    " . $email . "\n";
if (!empty($telefono)) {
    $body .= "Teléfono:  " . $telefono . "\n";
}
$body .= "\nMensaje:\n" . $mensaje . "\n";

$host = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']) : 'localhost';
if (empty($host) || $host === 'localhost') { $host = 'dominio.com'; }

$headers  = "From: noreply@" . $host . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Return-Path: " . $recipient_email . "\r\n";

$sent = @mail($recipient_email, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar por mail().']);
}
?>

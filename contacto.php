<?php
/*
 * contacto.php — Generado por Landing Page Builder
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$recipient_email = 'cmarcelohernandez@gmail.com';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

$nombre   = isset($_POST['nombre'])   ? sanitize($_POST['nombre'])   : '';
$email    = isset($_POST['email'])    ? sanitize($_POST['email'])    : '';
$telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : '';
$mensaje  = isset($_POST['mensaje'])  ? sanitize($_POST['mensaje'])  : '';

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

$headers  = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Return-Path: " . $recipient_email . "\r\n";

$sent = mail($recipient_email, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje. Por favor, intenta más tarde.']);
}
?>

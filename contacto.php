<?php
/*
 * contacto.php — Generado por Landing Page Builder (Protegido)
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Responder inmediatamente a peticiones preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

// 1. Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// 2. Trampa Honeypot contra Bots automatizados (campo oculto invisible para humanos)
if (!empty($_POST['website_url']) || !empty($_POST['_gotcha'])) {
    // Si un bot completó el campo trampa, responder éxito falso silencioso sin enviar correo
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
    exit;
}

// 3. Rate Limiting por IP (Protección contra inundaciones / DoS / Mail bombing)
// Máximo 5 envíos cada 10 minutos por dirección IP
$client_ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) 
    ? $_SERVER['HTTP_CF_CONNECTING_IP'] 
    : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
$client_ip = trim($client_ip);

$rate_dir = sys_get_temp_dir() . '/cmh_rate_limits';
if (!file_exists($rate_dir)) {
    @mkdir($rate_dir, 0755, true);
}
$rate_file = $rate_dir . '/' . md5($client_ip) . '.json';
$now = time();
$window = 600; // 10 minutos
$max_attempts = 5;

$attempts = [];
if (file_exists($rate_file)) {
    $raw = @file_get_contents($rate_file);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        foreach ($decoded as $timestamp) {
            if (($now - $timestamp) < $window) {
                $attempts[] = $timestamp;
            }
        }
    }
}

if (count($attempts) >= $max_attempts) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Has alcanzado el límite de envíos. Por favor espera 10 minutos.']);
    exit;
}

$recipient_email = 'cmarcelohernandez@gmail.com';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Soporte para application/json y multipart/form-data
$input = json_decode(file_get_contents('php://input'), true);
if (is_array($input)) {
    if (!empty($input['website_url']) || !empty($input['_gotcha'])) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
        exit;
    }
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

// 4. Blindaje contra Email Header Injection (eliminar saltos de línea maliciosos en campos de cabecera)
$nombre   = preg_replace('/[
	]+/', ' ', mb_substr(trim($nombre), 0, 100));
$email    = preg_replace('/[
	]+/', '', trim($email));
$telefono = preg_replace('/[
	]+/', ' ', mb_substr(trim($telefono), 0, 50));
$mensaje  = mb_substr(trim($mensaje), 0, 4000); // Límite de 4000 caracteres

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
$body .= "\nMensaje:\n" . $mensaje . "\n\n";
$body .= "---\nIP: " . $client_ip . "\nFecha: " . date('Y-m-d H:i:s') . "\n";

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
    // Registrar intento para el rate limit
    $attempts[] = $now;
    @file_put_contents($rate_file, json_encode($attempts));
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar por mail().']);
}
?>

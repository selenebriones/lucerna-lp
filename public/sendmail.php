<?php
header('Content-Type: application/json; charset=utf-8');

// Permiso para CORS si es necesario (generalmente no lo es si está en el mismo dominio)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilamos el payload enviado via fetch JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Si json_decode falla, usar variable $_POST por precaución
    if (!$data) {
        $data = $_POST;
    }

    $nombre = strip_tags(trim($data["nombre"] ?? ''));
    $email = filter_var(trim($data["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = strip_tags(trim($data["telefono"] ?? ''));
    $solucion = strip_tags(trim($data["solucion"] ?? ''));
    $ciudad = strip_tags(trim($data["ciudad"] ?? ''));

    // Validacion Basica en Servidor
    if (empty($nombre) || empty($email) || empty($telefono) || empty($solucion) || empty($ciudad)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Ocurrió un error. Campos incompletos."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Ocurrió un error. Correo inválido."]);
        exit;
    }

    $recipient = "selene.briones@kodex.mx";
    $subject = "Nueva Solicitud de Cotización WEB - Lucerna Energy";

    $email_content = "¡Tienes una nueva solicitud de prospecto desde el formulario del sitio web!\n\n";
    $email_content .= "== DATOS DEL CONTACTO ==\n";
    $email_content .= "Nombre y Apellido: $nombre\n";
    $email_content .= "Correo Electrónico: $email\n";
    $email_content .= "Número de Teléfono: $telefono\n";
    $email_content .= "Tipo de Solución Deseada: $solucion\n";
    $email_content .= "Ubicación/Ciudad: $ciudad\n\n";
    $email_content .= "---------------------------------------\n";
    $email_content .= "Este correo se ha generado automáticamente por el sitio web lucernaenergy.mx";

    $email_headers = "From: webmaster@lucernaenergy.mx\r\n";
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8";

    // Intentamos enviar el correo con la funciona nativa de PHP
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "No se pudo enviar el correo desde el servidor PHP."]);
    }
} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Acceso denegado. Método no permitido."]);
}
?>

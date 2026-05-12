<?php
require_once 'lib/email/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Se esperan las variables $reserva y $usuario inyectadas desde EmailReservaController
if (!isset($reserva) || !isset($usuario)) {
    return;
}

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug  = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'alissonflorezaroca@gmail.com';
    $mail->Password   = 'aije sokv zcup mplg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->isHTML(true);

    $emailDest  = $usuario['email']  ?? null;
    $nombre     = $usuario['nombre'] . ' ' . $usuario['apellido'];

    if (!$emailDest || !$nombre) {
        return;
    }

    date_default_timezone_set('America/Bogota');
    $fechaEnvio = date('d \d\e F \d\e Y');
    $horaEnvio  = date('H:i');

    // Datos de la reserva
    $habitacion   = htmlspecialchars($reserva['habitacion']  ?? 'N/A');
    $tipo         = htmlspecialchars($reserva['tipo']        ?? 'N/A');
    $descripcion  = htmlspecialchars($reserva['descripcion'] ?? 'Sin descripción.');
    $entrada      = htmlspecialchars($reserva['entrada']     ?? 'N/A');
    $salida       = htmlspecialchars($reserva['salida']      ?? 'N/A');
    $noches       = htmlspecialchars($reserva['noches']      ?? '0');
    $personas     = htmlspecialchars($reserva['personas']    ?? '1');
    $pago         = htmlspecialchars(ucfirst($reserva['pago'] ?? 'N/A'));
    $total        = '$' . number_format($reserva['total'] ?? 0, 0, ',', '.');
    $createdAt    = htmlspecialchars($reserva['created_at']  ?? date('Y-m-d H:i:s'));
    $documento    = htmlspecialchars($usuario['documento']   ?? 'N/A');
    $tipoDoc      = htmlspecialchars($usuario['tipo_documento'] ?? 'Documento');
    $telefono     = htmlspecialchars($usuario['telefono']    ?? 'N/A');
    $emailMostrar = htmlspecialchars($emailDest);

    $mail->setFrom('alissonflorezaroca@gmail.com', 'Viña del Mar');
    $mail->addAddress($emailDest, $nombre);
    $mail->Subject = '✦ ¡Reserva Confirmada en Viña del Mar! 🏨';

    $mail->Body = "
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <title>Confirmación de Reserva</title>
</head>
<body style='margin:0; padding:0; background-color:#f0f2f5; font-family:Arial, sans-serif;'>
  <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#f0f2f5; padding:20px 0;'>
    <tr>
      <td align='center'>

        <!-- Contenedor principal -->
        <table width='600' border='0' cellspacing='0' cellpadding='0'
               style='background-color:#ffffff; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);'>

          <!-- Barra de marca superior -->
          <tr>
            <td style='background-color:#0c2444; padding:20px; text-align:center;'>
              <h2 style='margin:0; color:#f5edd8; font-size:18px; font-weight:300; letter-spacing:6px; text-transform:uppercase; font-family:Georgia,serif;'>Hotel Viña del Mar</h2>
              <p style='margin:5px 0 0; color:#c9a84c; font-size:10px; letter-spacing:4px; font-weight:bold;'>CONFIRMACIÓN DE RESERVA</p>
            </td>
          </tr>

          <!-- Imagen de cabecera -->
          <tr>
            <td style='background-color:#0c2444;'>
              <img src='https://images.pexels.com/photos/271618/pexels-photo-271618.jpeg?auto=compress&cs=tinysrgb&w=600&h=260&fit=crop'
                   alt='Hotel Viña del Mar' width='600' border='0'
                   style='display:block; width:100%; max-width:600px; height:auto; border:0;'>
            </td>
          </tr>

          <!-- Ícono flotante -->
          <tr>
            <td align='center' style='height:40px;'>
              <div style='margin-top:-45px;'>
                <img src='https://cdn-icons-png.flaticon.com/512/3903/3903806.png'
                     alt='Reserva' width='80' height='80' border='0'
                     style='display:block; background-color:#ffffff; border-radius:50%; box-shadow:0 5px 15px rgba(0,0,0,0.2);'>
              </div>
            </td>
          </tr>

          <!-- Contenido principal -->
          <tr>
            <td style='padding:20px 50px 40px; text-align:center;'>

              <h1 style='margin:0; color:#0c2444; font-size:26px; font-weight:bold;'>¡Reserva Confirmada! 🎉</h1>

              <!-- Divider dorado -->
              <table width='80' border='0' cellspacing='0' cellpadding='0' align='center' style='margin:20px auto;'>
                <tr><td style='height:1px; background-color:#c9a84c;'></td></tr>
                <tr><td align='center'><div style='width:8px; height:8px; background-color:#c9a84c; border-radius:50%; margin-top:-4px;'></div></td></tr>
              </table>

              <p style='margin:0; color:#0c2444; font-size:19px; font-weight:600;'>Hola, $nombre 👋</p>
              <p style='margin:12px 0 30px; color:#555555; font-size:13px; line-height:1.7;'>
                Tu reserva ha sido registrada exitosamente en <span style='color:#0c2444; font-weight:bold;'>Hotel Viña del Mar</span>.
                A continuación encontrarás todos los detalles de tu estancia. ¡Te esperamos!
              </p>

              <!-- ── BLOQUE: Datos del cliente ── -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0'
                     style='background-color:#f4f9ff; border-radius:12px; margin-bottom:20px;'>
                <tr>
                  <td style='padding:20px 25px;'>
                    <p style='margin:0 0 15px; color:#0c2444; font-size:13px; font-weight:bold;
                               text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #c9a84c; padding-bottom:8px;'>
                      👤 Datos del Cliente
                    </p>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/1077/1077114.png' width='16' style='vertical-align:middle; opacity:0.6; margin-right:6px;'>Nombre completo
                        </td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$nombre</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/542/542689.png' width='16' style='vertical-align:middle; opacity:0.6; margin-right:6px;'>Correo electrónico
                        </td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$emailMostrar</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/747/747376.png' width='16' style='vertical-align:middle; opacity:0.6; margin-right:6px;'>$tipoDoc
                        </td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$documento</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; font-size:13px; color:#777;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/455/455705.png' width='16' style='vertical-align:middle; opacity:0.6; margin-right:6px;'>Teléfono
                        </td>
                        <td style='padding:9px 0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$telefono</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- ── BLOQUE: Detalles de la reserva ── -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0'
                     style='background-color:#f4f9ff; border-radius:12px; margin-bottom:20px;'>
                <tr>
                  <td style='padding:20px 25px;'>
                    <p style='margin:0 0 15px; color:#0c2444; font-size:13px; font-weight:bold;
                               text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #c9a84c; padding-bottom:8px;'>
                      🏨 Detalles de la Reserva
                    </p>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>🛏️ Habitación</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$habitacion</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>🏷️ Tipo</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$tipo</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>📝 Descripción</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#555; text-align:right;'>$descripcion</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>📅 Fecha de entrada</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$entrada</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>📅 Fecha de salida</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$salida</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>🌙 Noches</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$noches noche(s)</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#777;'>👥 Huéspedes</td>
                        <td style='padding:9px 0; border-bottom:1px solid #e1e8f0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$personas persona(s)</td>
                      </tr>
                      <tr>
                        <td style='padding:9px 0; font-size:13px; color:#777;'>💳 Método de pago</td>
                        <td style='padding:9px 0; font-size:13px; color:#0c2444; font-weight:bold; text-align:right;'>$pago</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- ── BLOQUE: Total a pagar ── -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0'
                     style='background-color:#0c2444; border-radius:12px; margin-bottom:25px;'>
                <tr>
                  <td style='padding:22px 30px;'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td style='font-size:15px; color:#ffffff; font-weight:bold;'>TOTAL A PAGAR</td>
                        <td style='font-size:26px; color:#c9a84c; font-weight:bold; text-align:right;'>$total</td>
                      </tr>
                      <tr>
                        <td colspan='2' style='font-size:11px; color:#6b7c93; padding-top:6px;'>Fecha de creación de la reserva: $createdAt</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- ── BLOQUE: CTA ── -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0'
                     style='background-color:#fff9e6; border-radius:12px; border:1px solid #ffeeba;'>
                <tr>
                  <td style='padding:18px 20px;'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td width='45' valign='top'>
                          <img src='https://cdn-icons-png.flaticon.com/512/3135/3135706.png' width='38'>
                        </td>
                        <td style='padding:0 12px;'>
                          <p style='margin:0; font-size:13px; color:#856404; font-weight:bold;'>¿Quieres gestionar tu reserva?</p>
                          <p style='margin:4px 0 0; font-size:12px; color:#856404; line-height:1.4;'>Ingresa a tu portal y consulta, modifica o descarga el comprobante en PDF.</p>
                        </td>
                        <td width='120' align='right'>
                          <a href='http://localhost/Ejercicio-Hotel_Mvc/'
                             style='background-color:#0c2444; color:#ffffff; padding:11px 14px; border-radius:8px;
                                    text-decoration:none; font-size:11px; font-weight:bold; display:inline-block;'>
                            Ir al portal
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <div style='margin-top:35px;'>
                <p style='margin:0; font-size:22px; color:#e0e0e0;'>♡</p>
                <p style='margin:10px 0 0; color:#0c2444; font-size:14px; font-weight:bold;'>Gracias por elegir Hotel Viña del Mar 💙</p>
                <p style='margin:5px 0 0; color:#888; font-size:12px;'>Te esperamos para brindarte una experiencia inolvidable.</p>
              </div>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style='background-color:#0c2444; padding:50px 40px; text-align:center;'>
              <div style='width:40px; height:2px; background-color:#c9a84c; margin:0 auto 22px;'></div>
              <img src='https://cdn-icons-png.flaticon.com/512/8334/8334315.png' width='55' border='0'
                   style='display:inline-block; margin-bottom:20px;'>
              <p style='margin:0 0 18px; color:#ffffff; font-size:13px; letter-spacing:3px; text-transform:uppercase;'>Hotel Viña del Mar</p>
              <div style='margin-bottom:25px;'>
                <a href='#' style='text-decoration:none; margin:0 10px;'><img src='https://cdn-icons-png.flaticon.com/512/733/733547.png' width='22' border='0' alt='FB'></a>
                <a href='#' style='text-decoration:none; margin:0 10px;'><img src='https://cdn-icons-png.flaticon.com/512/2111/2111463.png' width='22' border='0' alt='IG'></a>
                <a href='#' style='text-decoration:none; margin:0 10px;'><img src='https://cdn-icons-png.flaticon.com/512/515/515636.png' width='22' border='0' alt='Web'></a>
              </div>
              <p style='margin:0; font-size:11px; color:#6b7c93; letter-spacing:1px;'>reservas@hotelvinadelmar.cl &nbsp;|&nbsp; +56 32 000 0000</p>
              <p style='margin:6px 0 0; font-size:10px; color:#6b7c93;'>Av. San Martín 199, Viña del Mar, Chile</p>
              <p style='margin:10px 0 0; font-size:10px; color:#4a5b71;'>© 2026 Viña del Mar — Todos los derechos reservados.</p>
              <p style='margin:8px 0 0; font-size:10px; color:#4a5b71;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
";

    $mail->send();

} catch (Exception $e) {
    // Error silencioso — el flujo de reserva no se interrumpe
}
?>

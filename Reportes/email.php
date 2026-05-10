<?php
require_once 'lib/email/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

    // Datos de la sesión
    $email  = $_SESSION['email_temp'] ?? 'usuario@ejemplo.com';
    $nombre = $_SESSION['nombre_temp'] ?? 'Huésped';
    $fecha  = date('d \d\e F \d\e Y');

    $mail->setFrom('alissonflorezaroca@gmail.com', 'Viña del Mar');
    $mail->addAddress($email, $nombre);
    $mail->Subject = '✦ ¡Registro Exitoso en Viña del Mar! 👋';

    // ─── HTML BODY ────────────────────────────────────────────────────────────
    $mail->Body = "
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <title>Registro Exitoso</title>
</head>
<body style='margin:0; padding:0; background-color:#f0f2f5; font-family:Arial, sans-serif;'>
  <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#f0f2f5; padding: 20px 0;'>
    <tr>
      <td align='center'>
        
        <!-- Main Container -->
        <table width='600' border='0' cellspacing='0' cellpadding='0' style='background-color:#ffffff; border-radius:15px; overflow:hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);'>
          
          <!-- Header Title (Nuevo) -->
          <tr>
            <td style='background-color:#0c2444; padding:20px; text-align:center;'>
                <h2 style='margin:0; color:#f5edd8; font-size:18px; font-weight:300; letter-spacing:6px; text-transform:uppercase; font-family:\"Georgia\", serif;'>Hotel Viña del Mar</h2>
                <p style='margin:5px 0 0; color:#c9a84c; font-size:10px; letter-spacing:4px; font-weight:bold;'>VIVE LA EXPERIENCIA</p>
            </td>
          </tr>

          <!-- Header with Image -->
          <tr>
            <td style='background-color:#0c2444;'>
              <img src='https://images.pexels.com/photos/189333/pexels-photo-189333.jpeg?auto=compress&cs=tinysrgb&w=600&h=280&fit=crop' alt='Viña del Mar' width='600' border='0' style='display:block; width:100%; max-width:600px; height:auto; border:0;'>
            </td>
          </tr>
          
          <!-- Floating Icon Row -->
          <tr>
            <td align='center' style='padding:0; height:40px;'>
              <div style='margin-top:-45px;'>
                <img src='https://cdn-icons-png.flaticon.com/512/3106/3106854.png' alt='Lock' width='80' height='80' border='0' style='display:block; background-color:#ffffff; border-radius:50%; border:5px solid #ffffff; box-shadow:0 5px 15px rgba(0,0,0,0.2);'>
              </div>
            </td>
          </tr>

          <!-- Main Content -->
          <tr>
            <td style='padding: 20px 50px 40px; text-align:center;'>
              
              <h1 style='margin:0; color:#0c2444; font-size:28px; font-weight:bold; letter-spacing: -0.5px;'>¡Registro exitoso! 👋</h1>
              
              <!-- Custom Divider -->
              <table width='100' border='0' cellspacing='0' cellpadding='0' align='center' style='margin: 25px auto;'>
                <tr>
                  <td style='height:1px; background-color:#e0e0e0;'></td>
                </tr>
                <tr>
                  <td align='center' style='padding-top: -4px;'>
                    <div style='width:8px; height:8px; background-color:#c9a84c; border-radius:50%;'></div>
                  </td>
                </tr>
              </table>
              
              <p style='margin:0; color:#0c2444; font-size:20px; font-weight:600;'>Hola, $nombre 👋</p>
              <p style='margin:15px 0 35px; color:#555555; font-size:15px; line-height:1.7;'>
                Te confirmamos que te has registrado correctamente en tu cuenta de <span style='color:#0c2444; font-weight:bold;'>Viña del Mar</span>.<br>
                Estamos emocionados de que comiences esta experiencia con nosotros.
              </p>

              <!-- Details Box -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#f4f9ff; border-radius:15px; padding:25px;'>
                <tr>
                  <td>
                    <p style='margin:0 0 20px; color:#0c2444; font-size:14px; font-weight:bold; text-transform:uppercase; letter-spacing:1px;'>Detalles de la cuenta</p>
                    
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <!-- Row Nombre -->
                      <tr>
                        <td width='40' style='padding:12px 0; border-bottom: 1px solid #e1e8f0;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/1077/1077114.png' width='20' style='opacity:0.6;'>
                        </td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#777777;'>Nombre</td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#0c2444; font-weight:bold; text-align:right;'>$nombre</td>
                      </tr>
                      <!-- Row Correo -->
                      <tr>
                        <td width='40' style='padding:12px 0; border-bottom: 1px solid #e1e8f0;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/542/542689.png' width='20' style='opacity:0.6;'>
                        </td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#777777;'>Correo electrónico</td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#0c2444; font-weight:bold; text-align:right;'>$email</td>
                      </tr>
                      <!-- Row Estado (Nuevo) -->
                      <tr>
                        <td width='40' style='padding:12px 0; border-bottom: 1px solid #e1e8f0;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/2983/2983804.png' width='20' style='opacity:0.6;'>
                        </td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#777777;'>Estado de cuenta</td>
                        <td style='padding:12px 0; border-bottom: 1px solid #e1e8f0; font-size:14px; color:#28a745; font-weight:bold; text-align:right;'>ACTIVA ✓</td>
                      </tr>
                      <!-- Row Fecha -->
                      <tr>
                        <td width='40' style='padding:12px 0;'>
                          <img src='https://cdn-icons-png.flaticon.com/512/747/747376.png' width='20' style='opacity:0.6;'>
                        </td>
                        <td style='padding:12px 0; font-size:14px; color:#777777;'>Fecha de registro</td>
                        <td style='padding:12px 0; font-size:14px; color:#0c2444; font-weight:bold; text-align:right;'>$fecha</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Welcome Action Box -->
              <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#fff9e6; border-radius:15px; margin-top:30px; border:1px solid #ffeeba;'>
                <tr>
                  <td style='padding:20px; text-align:left;'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td width='50' valign='top'>
                          <img src='https://cdn-icons-png.flaticon.com/512/1067/1067561.png' width='40'>
                        </td>
                        <td style='padding:0 15px;'>
                          <p style='margin:0; font-size:14px; color:#856404; font-weight:bold;'>¿Listo para comenzar?</p>
                          <p style='margin:5px 0 0; font-size:12px; color:#856404; line-height:1.4;'>Ya puedes acceder a todos nuestros servicios exclusivos y gestionar tus reservas.</p>
                        </td>
                        <td width='120' align='right'>
                          <a href='http://localhost/Ejercicio-Hotel_Mvc/' style='background-color:#0c2444; color:#ffffff; padding:12px 15px; border-radius:8px; text-decoration:none; font-size:12px; font-weight:bold; display:inline-block;'>Ir al portal</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <div style='margin-top:40px;'>
                <p style='margin:0; font-size:24px; color:#e0e0e0;'>♡</p>
                <p style='margin:10px 0 0; color:#0c2444; font-size:15px; font-weight:bold;'>Gracias por confiar en Viña del Mar 💙</p>
                <p style='margin:5px 0 0; color:#888888; font-size:13px;'>Esperamos que disfrutes de una experiencia increíble.</p>
              </div>

            </td>
          </tr>

          <!-- Footer Organizado y Elegante (Mejorado) -->
          <tr>
            <td style='background-color:#0c2444; padding:60px 40px; text-align:center;'>
              
              <!-- Divider Gold en Footer -->
              <div style='width:40px; height:2px; background-color:#c9a84c; margin:0 auto 25px;'></div>

              <img src='https://cdn-icons-png.flaticon.com/512/3211/3211425.png' width='60' border='0' style='display:inline-block; margin-bottom:25px;'>
              
              <p style='margin:0 0 20px; color:#ffffff; font-size:14px; letter-spacing:3px; text-transform:uppercase;'>Hotel Viña del Mar</p>

              <div style='margin-bottom:30px;'>
                <a href='#' style='text-decoration:none; margin:0 12px;'><img src='https://cdn-icons-png.flaticon.com/512/733/733547.png' width='24' border='0' alt='FB'></a>
                <a href='#' style='text-decoration:none; margin:0 12px;'><img src='https://cdn-icons-png.flaticon.com/512/2111/2111463.png' width='24' border='0' alt='IG'></a>
                <a href='#' style='text-decoration:none; margin:0 12px;'><img src='https://cdn-icons-png.flaticon.com/512/1006/1006771.png' width='24' border='0' alt='Web'></a>
              </div>
              
              <p style='margin:0; font-size:11px; color:#6b7c93; letter-spacing:1px;'>© 2026 Viña del Mar — Todos los derechos reservados.</p>
              <p style='margin:10px 0 0; font-size:10px; color:#4a5b71;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
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
    // Error silencioso
}
?>
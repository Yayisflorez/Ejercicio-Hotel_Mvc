<?php

class EmailLoginController {

    /**
     * Envía el correo de notificación de inicio de sesión al usuario.
     *
     * @param string $emailDest  Correo electrónico del usuario.
     * @param string $nombre     Nombre completo del usuario.
     */
    public function sendEmailLogin($emailDest, $nombre) {
        // Inyectamos las variables que EmailLogin.php necesita como variables locales
        require 'Reportes/EmailLogin.php';
    }
}
?>

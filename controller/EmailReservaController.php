<?php

require_once 'model/Usuario.php';
require_once 'model/Reserva.php';

class EmailReservaController {

    /**
     * Envía el correo de confirmación al usuario cuando crea una reserva.
     *
     * @param array $reserva  Array con los datos de la reserva (misma estructura que usa reportes.php).
     * @param array $usuario  Array con los datos del usuario (nombre, apellido, email, etc.).
     */
    public function sendEmailReserva($reserva, $usuario) {
        // Inyectamos las variables que EmailReserva.php necesita como variables locales
        require 'Reportes/EmailReserva.php';
    }
}
?>

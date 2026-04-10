<?php

class conexion
{

    private $conexionDb;

    public function conectar()
    {
        $host = "localhost";
        $db = "hotel";
        $user = "root";
        $pass = "";

        try {
            $this->conexionDb = new mysqli($host, $user, $pass, $db);

            // Verificar conexión
            if ($this->conexionDb->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexionDb->connect_error);
            }

            // Establecer charset
            $this->conexionDb->set_charset("utf8");

        } catch (Exception $e) {
            die($e->getMessage());
        }


    }

    public function getConexion(){
        return $this->conexionDb;
    }

    public function cerrar(){
        $this->conexionDb = null;
    }


}

?>

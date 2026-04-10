<?php
require_once("model/conexion.php");

class ControllerBase
{

    public function verPaginaInicio($pagina)
    {
        include_once $pagina;
    }

    public function registerUser()
    {
        $conexionObj = new Conexion();
        $conexionObj->conectar();
        $conexion = $conexionObj->getConexion();
        

        $errors = [];

        $Nombre = trim($_POST['nombre'] ?? '');
        $Apellido = trim($_POST['apellido'] ?? '');
        $Email = trim($_POST['email'] ?? '');
        $Telefono = trim($_POST['telefono'] ?? '');
        $TipoDoc = trim($_POST['tipo_doc'] ?? '');
        $Documento = trim($_POST['documento'] ?? '');
        $Contraseña1 = $_POST['password'] ?? '';
        $Contraseña2 = $_POST['confirm_password'] ?? '';

        // Guardar datos para devolverlos al form
        $_SESSION['datos'] = [
            'nombre' => $Nombre,
            'apellido' => $Apellido,
            'email' => $Email,
            'telefono' => $Telefono,
            'tipo_doc' => $TipoDoc,
            'documento' => $Documento
        ];

        // VALIDACIONES

        if (empty($Nombre)) {
            $errors['nombre'] = "El nombre es obligatorio";
        } elseif (mb_strlen($Nombre) < 3) {
            $errors['nombre'] = "El nombre debe tener al menos 3 caracteres";
        }
        if (empty($Apellido)) {
            $errors['apellido'] = "El apellido es obligatorio";
        }
        if (empty($TipoDoc)) {
            $errors['tipo_doc'] = "El tipo de documento es obligatorio";
        }
        if (empty($Documento)) {
            $errors['documento'] = "El número de documento es obligatorio";
        }
        if (empty($Telefono)) {
            $errors['telefono'] = "El teléfono es obligatorio";
        }

        if (empty($Email)) {
            $errors['email'] = "El email es obligatorio";
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email no válido";
        }

        if (empty($Contraseña1)) {
            $errors['password'] = "La contraseña es obligatoria";
        } elseif (mb_strlen($Contraseña1) < 8) {
            $errors['password'] = "Mínimo 8 caracteres";
        }

        if (empty($Contraseña2)) {
            $errors['confirm_password'] = "Confirma la contraseña";
        } elseif ($Contraseña1 !== $Contraseña2) {
            $errors['confirm_password'] = "No coinciden";
        }



        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;

            header("Location: index.php?action=getFormRegisterUser");
            exit;
        }

        // Validar email y documento únicos (ambos errores a la vez)
        $sql = "SELECT email, documento FROM usuarios WHERE email = ? OR documento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ss', $Email, $Documento);
        $stmt->execute();
        $result = $stmt->get_result();
        $hayError = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['email'] === $Email) {
                $_SESSION['errors']['email'] = "El email ya está registrado";
                $hayError = true;
            }
            if ($row['documento'] === $Documento) {
                $_SESSION['errors']['documento'] = "El número de documento ya está registrado";
                $hayError = true;
            }
        }
        if ($hayError) {
            header("Location: index.php?action=getFormRegisterUser");
            exit;
        }

        // Insertar usuario
        $passwordHash = password_hash($Contraseña1, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, apellido, email, telefono, tipo_doc, documento, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('sssssss', $Nombre, $Apellido, $Email, $Telefono, $TipoDoc, $Documento, $passwordHash);
        if (!$stmt->execute()) {
            $_SESSION['errors']['db'] = "Error al guardar: " . $stmt->error;
            header("Location: index.php?action=getFormRegisterUser");
            exit;
        }
        $_SESSION['mensaje'] = "¡Usuario creado con éxito!";
        $_SESSION['tipo'] = "success";
        $_SESSION['datos'] = [];
        header("Location: index.php?action=getFormRegisterUser&success=1");
        exit;
    }

    public function loginUser()
    {
        $errors = [];

        $Email = trim($_POST['email'] ?? '');
        $Contraseña = $_POST['password'] ?? '';

        if (empty($Email)) {
            $errors['email'] = "El email es obligatorio";
        }

        if (empty($Contraseña)) {
            $errors['password'] = "La contraseña es obligatoria";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['datos'] = ['email' => $Email];
            header("Location: index.php?action=getFormLoginUser");
            exit;
        }

        $conexionObj = new Conexion();
        $conexionObj->conectar();
        $conexion = $conexionObj->getConexion();

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('s', $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        $usuario = $result->fetch_assoc();

        if ($usuario && password_verify($Contraseña, $usuario['password'])) {

            $_SESSION['usuario'] = $usuario;

            header("Location: index.php?action=getFormInicioExitoso"); // o dashboard
            exit;

        } else {
            $_SESSION['errors']['general'] = "Credenciales incorrectas";
            header("Location: index.php?action=getFormLoginUser");
            exit;
        }
    }

    public function cerrarSesion()
    {

        $_SESSION = [];

        session_destroy();

        header("Location: index.php");
    }
}

?>
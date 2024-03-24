<?php
session_start();
include "funciones.php";

// Verifica si ya hay una sesión iniciada
if (isset($_SESSION["dni"])) {
    // Si ya hay una sesión iniciada, redirige a la página de inicio
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $contrasenya = $_POST["contrasenya"];

    $funciones = new Funciones();

    //Compruebo que el DNI cumple los requisitos
    if ($funciones->validarDNI($dni)) {
        try {
            //Consulta prepare
            $stmt = $con->prepare("SELECT dni, contrasenya, rol, nombre, direccion, localidad, provincia, telefono, email, activo 
                                    FROM clientes 
                                    WHERE dni = :dni");
            $stmt->bindParam(":dni", $dni);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                // Almacena el valor de activo en una variable de sesión
                $_SESSION["cliente_activo"] = $fila["activo"];

                // Verifica si el cliente está activo
                if ($_SESSION["cliente_activo"] == 1) {
                    // Verifico la contraseña
                    if (password_verify($contrasenya, $fila["contrasenya"])) {
                        // Almaceno la información del cliente en la sesión
                        $_SESSION["dni"] = $fila["dni"];
                        $_SESSION["rol"] = $fila["rol"];

                        // También puedes almacenar otros detalles del cliente si es necesario
                        $_SESSION["cliente"] = [
                            'dni' => $fila["dni"],
                            'nombre' => $fila["nombre"],
                            'direccion' => $fila["direccion"],
                            'localidad' => $fila["localidad"],
                            'provincia' => $fila["provincia"],
                            'telefono' => $fila["telefono"],
                            'email' => $fila["email"],
                            'activo' => $fila["activo"]
                        ];

                        // Redirige a la página de origen si está almacenada
                        if (isset($_SESSION['url_origen'])) {
                            $url_origen = $_SESSION['url_origen'];
                            unset($_SESSION['url_origen']); // Limpia la URL de origen
                            header("Location: $url_origen");
                            exit();
                        } else {
                            header("Location: index.php");
                            exit();
                        }
                    } else {
                        $error_message = "Error. Contraseña incorrecta.";
                    }
                } else {
                    // El usuario no está activo, destruye la sesión y muestra un mensaje de error
                    session_destroy();
                    $error_message = "Error. El usuario no está activo. Por favor, contacte al administrador.";
                }
            } else {
                $error_message = "Error. Vuelva a introducir los datos.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Error: DNI no válido.";
    }
}

include "header.php";
?>

<h2 class="text-center text-success">Debes iniciar sesión</h2>

<?php
include "footer.php";
$con = null;
?>
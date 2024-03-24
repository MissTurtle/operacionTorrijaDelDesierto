<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["dni"])) {
    $dni = $_GET["dni"];

    try {
        // Consulta SQL para obtener los datos
        $sql = "SELECT * FROM clientes WHERE dni = :dni";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(":dni", $dni);
        $stmt->execute();

        // Guardo la fila del cliente
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            // Envío al formulario
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmar"])) {
                // Compruebo si el DNI que ha iniciado sesión tiene el rol administrador
                if ($_SESSION["rol"] == "administrador" && $_SESSION["dni"] == $dni) {
                    echo "No puedes borrarte porque eres administrador.<br>";
                    echo '<a href="index.php">Volver</a>';
                    exit;
                }

                // Actualizo el campo "activo" a 0 en lugar de borrar al cliente
                $sqlActualizar = "UPDATE clientes SET activo = 0 WHERE dni = :dni";
                $stmtActualizar = $con->prepare($sqlActualizar);
                $stmtActualizar->bindParam(":dni", $dni);

                if ($stmtActualizar->execute()) {
                    // Compruebo si el DNI del cliente actual coincide con el DNI del cliente que inició sesión
                    if ($_SESSION['dni'] == $dni) {
                        // Si coincide, cierro la sesión
                        echo "El cliente " . $fila["nombre"] . " ha sido desactivado.";
                        header("refresh:2;url=logout.php");
                    } else {
                        // Si no coincide, redirijo a la página principal
                        echo "El cliente " . $fila["nombre"] . " ha sido desactivado.";
                        header("refresh:2;url=mostrarusuarios.php");
                    }
                } else {
                    echo "Error al desactivar el cliente.";
                }
            }
        } else {
            // Si el cliente no existe, redirecciona
            header("Location: index.php");
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
}

include "header.php";
?>

<main class="container">
    <h2>Desactivar cliente</h2>
    <p>¿Seguro que quieres desactivar al cliente <?php echo $fila["nombre"]; ?>?</p>
    <form name="formconf" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
        <input type="hidden" class="form-control" name="dni" value="<?php echo $dni; ?>">
        </div>
        <div class="mb-3">
        <input type="submit" class="btn btn-success text-black" name="confirmar" value="Confirmar"><br>
        </div>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
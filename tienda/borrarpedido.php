<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["idPedido"])) {
    $idPedido = $_GET["idPedido"];

    try {
        // Consulta SQL para obtener los datos
        $sql = "SELECT * FROM pedido WHERE idPedido = :idPedido";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(":idPedido", $idPedido);
        $stmt->execute();

        // Guardo la fila del pedido
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            // Envío al formulario
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmar"])) {

                // Actualizo el campo "activo" a 0 en lugar de borrar al pedido
                $sqlActualizar = "UPDATE pedido SET activo = 0 WHERE idPedido = :idPedido";
                $stmtActualizar = $con->prepare($sqlActualizar);
                $stmtActualizar->bindParam(":idPedido", $idPedido);

                if ($stmtActualizar->execute()) {
                    echo "El pedido " . $fila["idPedido"] . " ha sido desactivado.";
                    header("refresh:2;url=mostrarpedidos.php");
                    exit();
                } else {
                    echo "Error al desactivar el pedido.";
                }
            }
        } else {
            // Si el pedido no existe, redirecciona
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}

include "header.php";
?>

<main class="container">
<h2>Desactivar pedido</h2>
<p>¿Seguro que quieres desactivar al pedido <?php echo $fila["idPedido"]; ?>?</p>
<form name="formconf" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
    <input type="hidden" class="form-control" name="idPedido" value="<?php echo $idPedido; ?>">
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
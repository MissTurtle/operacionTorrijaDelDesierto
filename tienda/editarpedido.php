<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge los datos del formulario
    $idPedido = $_POST["idp"];
    $idCliente = $_POST["idc"];
    $total = $_POST["tot"];
    $fCreacion = $_POST["fcre"];
    $estado = isset($_POST["estado"]) ? intval($_POST["estado"]) : 0;
    $activo = isset($_POST["activo"]) ? 1 : 0;

    try {
        // Consulta SQL para actualizar el pedido
        $sql = "UPDATE pedido
                SET idCliente = :idCliente, total = :total, fCreacion = :fCreacion, estado = :estado, activo = :activo
                WHERE idPedido = :idPedido";

        // Consulta preparada
        $stmt = $con->prepare($sql);

        $stmt->bindParam(":idPedido", $idPedido);
        $stmt->bindParam(":idCliente", $idCliente);
        $stmt->bindParam(":total", $total);
        $stmt->bindParam(":fCreacion", $fCreacion);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":activo", $activo);

        $stmt->execute();

        header("Location: mostrarpedidos.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Cierra la conexión después de la consulta SELECT
try {
    $dni = $_GET["idPedido"];
    $sql = "SELECT * FROM pedido WHERE idPedido = :idPedido";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(":idPedido", $dni);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include "header.php";
?>

<main class="container">
    <h2>Introduce los nuevos datos del pedido</h2>
    <form name="formedi" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
        <input type="hidden" class="form-control" name="idp" value="<?php echo $fila['idPedido']; ?>">
        </div>
        <div class="mb-3">
        <label for="idc" class="form-label">ID Cliente:</label>
        <input type="text" class="form-control" name="idc" value="<?php echo $fila['idCliente']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="tot" class="form-label">Total:</label>
        <input type="text" class="form-control" name="tot" value="<?php echo $fila['total']; ?>" maxlength="50" required>
        </div>
        <div class="mb-3">
        <label for="fcre" class="form-label">Fecha de creacion:</label>
        <input type="text" class="form-control" name="fcre" value="<?php echo $fila['fCreacion']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <?php
                // Definir opciones de estado
                $opciones_estado = array(
                    0 => "Pendiente",
                    1 => "Pagado",
                    2 => "Enviado",
                    3 => "Recibido"
                );

                // Iterar sobre las opciones de estado
                foreach ($opciones_estado as $value => $label) {
                    // Verificar si el estado actual coincide con la opción
                    $selected = ($fila['estado'] == $value) ? "selected" : "";
                    // Imprimir opción con el estado actual seleccionado
                    echo "<option value=\"$value\" $selected>$label</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
        <label for="activo" class="form-check-label">Activo:</label>
        <input type="checkbox" class="form-check-input" name="activo" <?php echo $fila["activo"] ? "checked" : ""; ?>>
        </div>
        <input type="submit" class="btn btn-success text-black" name="actualizar" value="Actualizar Datos"><br><br>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
<?php
session_start();
include "funciones.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Recupero los detalles del pedido desde la base de datos
$orderID = $_GET['id'];
$query = $con->query("SELECT * FROM pedido WHERE idPedido = $orderID");
$orderDetails = $query->fetch(PDO::FETCH_ASSOC);

// Verifico si se encontro el pedido
if (!$orderDetails) {
    header("Location: index.php");
    exit();
}

include "header.php";
?>

<main class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>Estado de tu pedido</h2>
            <p class="text-success">Tu pedido ha sido enviado con exito. La ID del pedido es #<?php echo $orderDetails['idPedido']; ?>.</p>
        </div>
    </div>
</main>

<?php
include "footer.php";
$con = null;
?>
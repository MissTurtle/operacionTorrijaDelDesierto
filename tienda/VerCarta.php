<?php
include 'La-carta.php';
include "funciones.php";

$cart = new Cart;

include "header.php";
?>

<main class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>Tu carrito</h2>
            <table class="table table-responsive table-bordered table-striped align-middle text-center">
                <caption class="caption-bot">Tabla de pedido</caption>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($cart->total_items() > 0) {
                        // Obtengo los elementos del carrito de la sesion
                        $cartItems = $cart->contents();
                        foreach ($cartItems as $item) {
                    ?>
                    <tr>
                        <td><?php echo $item["name"]; ?></td>
                        <td><?php echo '€' . $item["price"] . ' EUR'; ?></td>
                        <td><?php echo $item['qty']; ?></td>
                        <td><?php echo '€' . $item["subtotal"] . ' EUR'; ?></td>
                        <td>
                            <a href="AccionCarta.php?action=removeCartItem&id=<?php echo $item["rowid"]; ?> "class="btn btn-danger" onclick="return confirm('¿Seguro que quieres borrar este artículo del carrito?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr><td colspan="5"><p>No hay nada en el carrito</p></td></tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><a href="index.php" class="btn btn-success">Continuar Comprando</a></td>
                        <td colspan="2"></td>
                        <?php if ($cart->total_items() > 0) { ?>
                            <td class="text-center"><strong>Total <?php echo '€' . $cart->total() . ' EUR'; ?></strong></td>
                            <td><a href="Pagos.php" class="btn btn-success btn-block">Pagar</a></td>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</main>

<?php
include "footer.php";
$con = null;
?>
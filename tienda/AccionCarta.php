<?php
include 'La-carta.php';
date_default_timezone_set("Europe/Madrid");
include "funciones.php";

$cart = new Cart;

if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['id'])) {
        $productID = $_REQUEST['id'];
        // Obtengo los detalles del producto
        $query = $con->query("SELECT * FROM articulos WHERE Codigo = '" . $productID . "'");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $itemData = array(
            'id' => $row['codigo'],
            'name' => $row['nombre'],
            'price' => $row['precio'],
            'qty' => 1
        );

        $insertItem = $cart->insert($itemData);
        $redirectLoc = $insertItem ? 'VerCarta.php' : 'index.php';
        header("Location: " . $redirectLoc);
    } elseif ($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['id'])) {
        $deleteItem = $cart->remove($_REQUEST['id']);
        header("Location: VerCarta.php");
    } elseif ($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0 && !empty($_SESSION['sessCustomerID'])) {
        // Inserto los datos del pedido en la base de datos
        $total = $cart->total();
        $fCreacion = date("Y-m-d H:i:s");
        echo "sessCustomerID == ".$_SESSION['sessCustomerID'];

        $insertOrder = $con->prepare("INSERT INTO pedido (idCliente, total, fCreacion) 
                                  VALUES (:idCliente, :total, :fCreacion)");

        $insertOrder->bindParam(":idCliente", $_SESSION['sessCustomerID'], PDO::PARAM_STR);
        $insertOrder->bindParam(":total", $total, PDO::PARAM_STR);
        $insertOrder->bindParam(":fCreacion", $fCreacion, PDO::PARAM_STR);

        $insertOrder->execute();

        if ($insertOrder) {
            $orderID = $con->lastInsertId();

            $sql = '';
            // Obtengo los articulos del carrito
            $cartItems = $cart->contents();
            foreach ($cartItems as $item) {
                $sql .= "INSERT INTO pedido_articulos (pedidoId, productoId, cantidad) 
                         VALUES ('" . $orderID . "', '" . $item['id'] . "', '" . $item['qty'] . "');";
            }

            // Inserto las posiciones de los pedidos en la base de datos
            $stmt = $con->prepare($sql);
            $stmt->execute();

            // Cierro el cursor para liberar el conjunto de resultados
            $insertOrder->closeCursor(); 

            if ($stmt) {
                $cart->destroy();
                header("Location: OrdenExito.php?id=$orderID");
            } else {
                header("Location: Pagos.php");
            }
        } else {
            header("Location: Pagos.php");
        }
    } else {
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}

$con = null;
?>
<?php
include 'La-carta.php';
include "funciones.php";

$cart = new Cart;

// Redirige a la pagina principal si el carrito esta vacio
if ($cart->total_items() <= 0) {
    header("Location: index.php");
    exit();
}

// Verifica si el cliente no está logeado
if (!isset($_SESSION['dni'])) {
    // Almacena la URL actual en $_SESSION
    $_SESSION['url_origen'] = $_SERVER['REQUEST_URI'];

// Verifica si se ha enviado un formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dni"])) {
    try {
        if ($dniValido) {
            // Realiza la consulta para obtener los datos del cliente
            $stmt = $con->prepare("SELECT dni, nombre, direccion, email FROM clientes WHERE dni = :dni");
            $stmt->bindParam(":dni", $dniValido);  
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                // Almacena la información del cliente en la sesión
                $_SESSION['sessCustomerID'] = $fila["dni"];
                $_SESSION['cliente'] = [
                    'dni' => $fila["dni"],
                    'nombre' => $fila["nombre"],
                    'direccion' => $fila["direccion"],
                    'email' => $fila["email"]
                ];

                // Almacena la letra del DNI en la sesión
                $_SESSION['letraDNI'] = substr($dniValido, -1);

                // Redirige a la URL de origen o la página de inicio de sesión si no hay URL de origen
                $url_origen = isset($_SESSION['url_origen']) ? $_SESSION['url_origen'] : 'login.php';
                header("Location: $url_origen");
                exit();
            } else {
                echo "Error. Cliente no encontrado.<br>";
            }
        } else {
            echo "Error. DNI no válido.<br>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
    } else {
        echo "Error. Debes iniciar sesión.<br>";
    
        // Almacena la URL de origen en la sesión
        $_SESSION['url_origen'] = $_SERVER['REQUEST_URI'];
    
        // Redirige a la página de inicio de sesión
        header('Location: login.php');
        exit();
    }
}

// Obtener la información del cliente desde la sesión
$cliente = isset($_SESSION['cliente']) ? $_SESSION['cliente'] : null;

include "header.php";

// Mostrar información del cliente
echo "<h2>Datos del Cliente</h2>";

if ($cliente) {
    echo "<p>DNI: {$cliente['dni']}</p>";
    echo "<p>Nombre: {$cliente['nombre']}</p>";
    echo "<p>Dirección: {$cliente['direccion']}</p>";
    echo "<p>Email: {$cliente['email']}</p>";
} else {
    echo "<p>Error al obtener la información del cliente.</p>";
}
$_SESSION['sessCustomerID'] = $cliente['dni'];
$sessCustomID = $_SESSION['sessCustomerID'];

// Obtén los detalles del cliente usando el ID de cliente de la sesión
$query = $con->query("SELECT * FROM clientes WHERE dni = '.$sessCustomID.'");
$custRow = $query->fetch(PDO::FETCH_ASSOC);
?>

<main class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>Este es tu pedido</h2>
            <table class="table table-responsive table-bordered table-striped align-middle text-center">
                <caption class="caption-bot">Tabla resumen del pedido</caption>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($cart->total_items() > 0) {
                        // Obtén los elementos del carrito de la sesión
                        $cartItems = $cart->contents();
                        foreach ($cartItems as $item) {
                    ?>
                            <tr>
                                <td><?php echo $item["name"]; ?></td>
                                <td><?php echo '€' . $item["price"] . ' EUR'; ?></td>
                                <td><?php echo $item["qty"]; ?></td>
                                <td><?php echo '€' . $item["subtotal"] . ' EUR'; ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr><td colspan="4"><p>No hay nada en el carrito</p></td></tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><a href="index.php" class="btn btn-success"> Continuar Comprando</a></td>
                        <td><a href="VerCarta.php" class="btn btn-success"> Ver Articulos</a></td>
                        <td><a href="AccionCarta.php?action=placeOrder" class="btn btn-success orderBtn">Realizar Pago</a</td>
                        <?php if ($cart->total_items() > 0) { ?>
                            <td class="text-center"><strong>Total <?php echo '€' . $cart->total() . ' EUR'; ?></strong></td>
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
<?php
session_start();
include "funciones.php";
include "cliente.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

include "header.php";
?>

<main class="container">
    <h2>Buscar cliente</h2>
    <form name="buscli" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
        <label for="dni" class="form-label">DNI:</label>
        <input type="text" class="form-control" name="dni" maxlength="9" required><br>
        </div>
        <div class="mb-3">
        <input type="submit" class="btn btn-success text-black" value="Buscar cliente"><br><br>
        </div>
    </form>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $buscar = $_POST["dni"];

            $funciones = new Funciones();
            $clientes = $funciones->obtenerClientes();

            //Compruebo que el DNI cumple los requisitos (nota: para no liarme con las variables, aqui he llamado buscar al DNI)
            if ($funciones->validarDNI($buscar)) {
                //Filtro el array para encontrar solo el cliente que quiero por DNI (array_values y array_filter sacados de php.net)
                $clienteEncontrado = array_values(array_filter($clientes, function ($cliente) use ($buscar) {
                    return $cliente->getDNI() == $buscar;
                }));
            }else {
                echo "Error: DNI no valido.";
            }
            //Muestro los datos
            if (!empty($clienteEncontrado)) {
                echo "<h2>Cliente encontrado:</h2>";
                echo "<p>DNI: " . $clienteEncontrado[0]->getDNI() . "</p>";
                echo "<p>Nombre: " . $clienteEncontrado[0]->getNombre() . "</p>";
                echo "<p>Direccion: " . $clienteEncontrado[0]->getDireccion() . "</p>";
                echo "<p>Localidad: " . $clienteEncontrado[0]->getLocalidad() . "</p>";
                echo "<p>Provincia: " . $clienteEncontrado[0]->getProvincia() . "</p>";
                echo "<p>Telefono: " . $clienteEncontrado[0]->getTelefono() . "</p>";
                echo "<p>Email: " . $clienteEncontrado[0]->getEmail() . "</p>";
                echo "<p>Activo: " . $clienteEncontrado[0]->getActivo() . "</p>";
            } else {
                echo "<p>Cliente no encontrado.</p>";
            }
        }
    ?>
</main>

<?php
include "footer.php";
$con = null;
?>
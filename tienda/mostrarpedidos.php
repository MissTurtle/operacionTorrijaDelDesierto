<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

// Saco el rol del usuario que ha iniciado sesión
$rolUsuario = isset($_SESSION["rol"]) ? $_SESSION["rol"] : "";

// Configuro la paginación
$PAGS = 4;
$pagina = 1;
$inicio = 0;

if (isset($_GET["pagina"])) {
    $pagina = $_GET["pagina"];
    $inicio = ($pagina - 1) * $PAGS;
}

try {
    // Consulta SQL
    if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
        // Mostrar todos los pedidos para administradores y editores
        $sql = "SELECT * FROM pedido";
    } elseif ($rolUsuario == "usuario") {
        // Mostrar solo los datos del usuario con ese DNI si es cliente
        $dniUsuario = $_SESSION["dni"];
        $sql = "SELECT p.* FROM pedido p
                INNER JOIN clientes c ON p.idCliente = c.dni
                WHERE c.dni = :dni";
    }

    // Obtengo el valor de orden usando la fecha de creacion (pongo por defecto ascendente)
    $orden = isset($_GET["orden"]) ? $_GET["orden"] : "asc";
    //Continuacion de la SQL para ordenar, por eso concateno
    $sql .= " ORDER BY fCreacion $orden";
    $sql .= " LIMIT :inicio, :PAGS";

    $stmt = $con->prepare($sql);

    if ($rolUsuario == "usuario") {
        $stmt->bindParam(":dni", $dniUsuario);
    }

    $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
    $stmt->bindParam(":PAGS", $PAGS, PDO::PARAM_INT);

    $stmt->execute();
    $pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    // Inicializa $pedido como un array vacío en caso de error
    $pedido = [];
}

include "header.php";
?>

<main class="container">
<h2>Pedidos</h2>
<table class="table table-responsive table-bordered table-striped align-middle text-center">
    <caption class="caption-bot">Tabla de pedidos</caption>
    <thead class="table-dark">
        <tr>
            <th scope="col">Id Pedido</th>
            <th scope="col">ID Cliente</th>
            <th scope="col">Total</th>
            <th scope="col">Fecha de creacion</th>
            <th scope="col">Estado</th>
            <th scope="col">Activo</th>
            <th scope="col">Editar</th>
            <th scope="col">Borrar</th>
        </tr>
    </thead> 

    <?php
    //Separo cada enlace de paginacion
    $stmt = $con->prepare("SELECT COUNT(*) as total FROM pedido");
    $stmt->execute();

    $totalResultados = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    $totalPaginas = ceil($totalResultados / $PAGS);

    foreach ($pedido as $fila) {
        echo "<tr>";
        echo "<td>{$fila['idPedido']}</td>";
        echo "<td>{$fila['idCliente']}</td>";
        echo "<td>{$fila['total']}</td>";
        echo "<td>{$fila['fCreacion']}</td>";
        echo "<td>{$fila['estado']}</td>";
        echo "<td>{$fila['activo']}</td>";
        echo "<td><a href='editarpedido.php?idPedido={$fila['idPedido']}'><img src='imgs/editar.png' alt='Editar' style='width: 50px; height: 50px;'></a></td>";
        echo "<td><a href='borrarpedido.php?idPedido={$fila['idPedido']}'><img src='imgs/borrar.png' alt='Borrar' style='width: 50px; height: 50px;'></a></td>";
        echo "</tr>";
    }
echo "</table>";
    ?>

<ul class="pagination">
    <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
        <li class="page-item">
            <a class="page-link text-success" href="?pagina=<?= $i ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>
</ul>
    <a href="?orden=asc" class="text-decoration-none text-success ' . ($orden == 'asc' ? 'selected' : '') . '">Fecha Asc | </a>
    <a href="?orden=desc" class="text-decoration-none text-success ' . ($orden == 'desc' ? 'selected' : '') . '">Fecha Desc</a><br>
</main>

<?php
include "footer.php";
$con = null;
?>
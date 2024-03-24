<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

//Saco el rol del usuario que ha iniciado sesion
$rolUsuario = isset($_SESSION["rol"]) ? $_SESSION["rol"] : "";

//Configuro la paginacion
$PAGS = 4;
$pagina = 1;
$inicio = 0;

if(isset($_GET["pagina"])){
    $pagina = $_GET["pagina"];
    $inicio = ($pagina - 1) * $PAGS;
}

$sql = "";

try {
    //Consulta SQL
    if ($rolUsuario == "administrador") {
        //Lo muestro todo de todos si es administrador
        $sql = "SELECT * FROM clientes";
    } elseif ($rolUsuario == "usuario" || $rolUsuario == "editor") {
        //Muestro solo los datos del usuario con ese DNI si es cliente o editor
        $dniUsuario = $_SESSION["dni"];
        $sql = "SELECT * FROM clientes WHERE dni = :dni";
    }

    //Obtengo el valor de orden usando el nombre (pongo por defecto ascendente)
    $orden = isset($_GET["orden"]) ? $_GET["orden"] : "asc";
    //Continuacion de la SQL para ordenar, por eso concateno
    $sql .= " ORDER BY nombre $orden";
    $sql .= " LIMIT :inicio, :PAGS";
    
    $stmt = $con->prepare($sql);

    if ($rolUsuario == "usuario" || $rolUsuario == "editor") {
        $stmt->bindParam(":dni", $dniUsuario);
    }

    $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
    $stmt->bindParam(":PAGS", $PAGS, PDO::PARAM_INT);

    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include "header.php";
?>

<main class="container">
<h2>Clientes</h2>
<table class="table table-responsive table-bordered table-striped align-middle text-center">
    <caption class="caption-bot">Tabla de usuarios</caption>
    <thead class="table-dark">
        <tr>
            <th scope="col">DNI</th>
            <th scope="col">Nombre</th>
            <th scope="col">Direccion</th>
            <th scope="col">Localidad</th>
            <th scope="col">Provincia</th>
            <th scope="col">Telefono</th>
            <th scope="col">E-mail</th>
            <th scope="col">Rol</th>
            <th scope="col">Acticvo</th>
            <th scope="col">Editar</th>
            <th scope="col">Borrar</th>
        </tr>
    </thead> 

    <?php
    //Separo cada enlace de paginacion
    $stmt = $con->prepare("SELECT COUNT(*) as total FROM clientes");
    $stmt->execute();

    $totalResultados = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    $totalPaginas = ceil($totalResultados / $PAGS);

    foreach ($clientes as $fila) {
        echo "<tr>";
        echo "<td>{$fila['dni']}</td>";
        echo "<td>{$fila['nombre']}</td>";
        echo "<td>{$fila['direccion']}</td>";
        echo "<td>{$fila['localidad']}</td>";
        echo "<td>{$fila['provincia']}</td>";
        echo "<td>{$fila['telefono']}</td>";
        echo "<td>{$fila['email']}</td>";
        echo "<td>{$fila['rol']}</td>";
        echo "<td>{$fila['activo']}</td>";
        echo "<td><a href='editarcliente.php?dni={$fila['dni']}'><img src='imgs/editar.png' alt='Editar' style='width: 50px; height: 50px;'></a></td>";
        echo "<td><a href='borrarcliente.php?dni={$fila['dni']}'><img src='imgs/borrar.png' alt='Borrar' style='width: 50px; height: 50px;'></a></td>";
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

<?php
//Estos enlaces solo los ven los administradores
if ($rolUsuario == "administrador") {
    echo '<a href="?orden=asc" class="text-decoration-none text-success ' . ($orden == 'asc' ? 'selected' : '') . '">Nombre Asc | </a>';
    echo '<a href="?orden=desc" class="text-decoration-none text-success ' . ($orden == 'desc' ? 'selected' : '') . '">Nombre Desc</a><br><br>';
    echo '<a href="buscarcliente.php" class="text-decoration-none text-success">Buscar Cliente</a><br>';
    echo '<a href="clientenuevo.php" class="text-decoration-none text-success">Cliente Nuevo</a><br>';
}
?>
</main>

<?php
include "footer.php";
$con = null;
?>
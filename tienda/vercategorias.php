<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

$rolUsuario = isset($_SESSION["rol"]) ? $_SESSION["rol"] : "invitado";

//Configuro la paginacion
$PAGS = 4;
$pagina = 1;
$inicio = 0;

if(isset($_GET["pagina"])){
    $pagina = $_GET["pagina"];
    $inicio = ($pagina - 1) * $PAGS;
}

$orden = isset($_GET['orden']) ? $_GET['orden'] : 'asc';

try {
    $sql = "SELECT * FROM categorias ORDER BY nombre $orden LIMIT :inicio, :PAGS";
    
    $stmt = $con->prepare($sql);

    $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
    $stmt->bindParam(":PAGS", $PAGS, PDO::PARAM_INT);

    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include "header.php";
?>

<main class="container">
    <h2>Categorías</h2>
    <table class="table table-responsive table-bordered table-striped align-middle text-center">
        <caption class="caption-bot">Tabla de categorías</caption>
        <thead class="table-dark">
            <tr>
                <th scope="col">ID Categoría</th>
                <th scope="col">Nombre</th>
                <th scope="col">ID Super</th>
                <th scope="col">Activo</th>
                <?php
                //Esto solo lo ven administradores y editores
                if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
                    echo "<th scope='col'>Editar</th>";
                    echo "<th scope='col'>Borrar</th>";
                }
                ?>
            </tr>
        </thead>

        <?php
        foreach ($categorias as $fila) {
            echo "<tr>";
            echo "<td>{$fila['id_categoria']}</td>";
            echo "<td>{$fila['nombre']}</td>";
            echo "<td>{$fila['id_super']}</td>";
            echo "<td>{$fila['activo']}</td>";
            //Esto solo lo ven administradores y editores
            if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
                echo "<td><a href='editarcategoria.php?id_categoria={$fila['id_categoria']}'><img src='imgs/editar.png' alt='Editar' style='width: 50px; height: 50px;'></a></td>";
                echo "<td><a href='borrarcategoria.php?id_categoria={$fila['id_categoria']}'><img src='imgs/borrar.png' alt='Borrar' style='width: 50px; height: 50px;'></a></td>";
            }
            echo "</tr>";
            }
        echo "</table>";
        ?>

    <ul class="pagination">
        <?php
        $stmt = $con->prepare("SELECT COUNT(*) as total FROM categorias");
        $stmt->execute();

        $totalResultados = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
        $totalPaginas = ceil($totalResultados / $PAGS);

        for ($i = 1; $i <= $totalPaginas; $i++) :
        ?>
            <li class="page-item">
                <a class="page-link text-success" href="?pagina=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
    
    <br><a href="?orden=asc" class="text-decoration-none text-success ' . ($orden == 'asc' ? 'selected' : '') . '">Nombre Asc | </a>
    <a href="?orden=desc" class="text-decoration-none text-success ' . ($orden == 'desc' ? 'selected' : '') . '">Nombre Desc</a><br><br>
    
    <?php
    //Estos enlaces solo los ven los administradores y editores
    if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
        echo '<a class="text-decoration-none text-success" href="crear_categoria.php">Crear Categorias</a><br>';
        echo '<a class="text-decoration-none text-success" href="crear_subcategoria.php">Crear Subcategorias</a><br>';
    }
    ?>

</main>

<?php
include "footer.php";
$con = null;
?>
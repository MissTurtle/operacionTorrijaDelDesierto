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

//Primero indico que quiero ordenar por nombre, luego las direcciones
$ordenarPor = isset($_GET["ordenarPor"]) ? $_GET["ordenarPor"] : "nombre";
$orden = isset($_GET["orden"]) ? $_GET["orden"] : "asc";

if(isset($_GET["pagina"])){
    $pagina = $_GET["pagina"];
    $inicio = ($pagina - 1) * $PAGS;
   }

try {
    //Consulta SQL para obtener los articulos con paginacion y ordenar
    $sql = "SELECT * 
            FROM articulos ORDER BY $ordenarPor $orden
            LIMIT :inicio, :PAGS";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
    $stmt->bindParam(":PAGS", $PAGS, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include "header.php";
?>

<main class="container">
    <h2>Artículos</h2>
    <table class="table table-responsive table-bordered table-striped align-middle text-center">
            <caption class="caption-bot">Tabla de artículos</caption>
            <thead class="table-dark">
                <tr>
            <th scope="col">Codigo</th>
            <th scope="col">Nombre</a></th>
            <th scope="col">Descripcion</th>
            <th scope="col">Categoria</th>
            <th scope="col">Precio</th>
            <th scope="col">Imagen</th>
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
        if ($stmt->rowCount() > 0) {
            foreach ($resultado as $fila) {
                echo "<tr>";
                echo "<td>{$fila['codigo']}</td>";
                echo "<td>{$fila['nombre']}</td>";
                echo "<td>{$fila['descripcion']}</td>";
                echo "<td>{$fila['categoria']}</td>";
                echo "<td>{$fila['precio']}</td>";
                echo "<td><img src='{$fila['imagen']}' alt='Imagen' style='max-width: 100px; max-height: 100px;'></td>";
                echo "<td>{$fila['activo']}</td>";
                //Esto solo lo ven administradores y editores
                if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
                    echo "<td><a href='editararticulo.php?codigo={$fila['codigo']}'><img src='imgs/editar.png' alt='Editar' style='width: 50px; height: 50px;'></a></td>";
                    echo "<td><a href='borrararticulo.php?codigo={$fila['codigo']}'><img src='imgs/borrar.png' alt='Borrar' style='width: 50px; height: 50px;'></a></td>";
                }
                echo "</tr>";
            }
        }
    echo "</table>";
    
    //Separo cada enlace de paginacion
    $sqlTotalArticulos = "SELECT COUNT(*) FROM articulos";
    $stmtTotalArticulos = $con->prepare($sqlTotalArticulos);
    $stmtTotalArticulos->execute();
    
    $totalArticulos = $stmtTotalArticulos->fetchColumn();
    $totalPaginas = ceil($totalArticulos / $PAGS);
    ?>
    
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
            <li class="page-item">
                <a class="page-link text-success" href="?pagina=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
    
    <a href="?orden=asc" class="text-decoration-none text-success ' . ($orden == 'asc' ? 'selected' : '') . '">Nombre Asc | </a>
    <a href="?orden=desc" class="text-decoration-none text-success ' . ($orden == 'desc' ? 'selected' : '') . '">Nombre Desc</a><br><br>
    <?php
//Estos enlaces solo los ven los administradores y editores
if ($rolUsuario == "administrador" || $rolUsuario == "editor") {
    echo '<a class="text-decoration-none text-success" href="altaarticulos.php">Articulo Nuevo</a><br>';
}
?>
</main>

<?php
include "footer.php";
$con = null;
?>
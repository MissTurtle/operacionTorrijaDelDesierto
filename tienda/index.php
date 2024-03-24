<?php
include 'La-carta.php';
include "funciones.php";
include "header.php";

$cart = new Cart;

// Configuro la paginación
$PAGS = 4;
$pagina = 1;
$inicio = 0;

if(isset($_GET["pagina"])){
    $pagina = $_GET["pagina"];
    $inicio = ($pagina - 1) * $PAGS;
}

// Indica que deseas ordenar por nombre, luego las direcciones
$ordenarPor = isset($_GET["ordenarPor"]) ? $_GET["ordenarPor"] : "nombre";
$orden = isset($_GET["orden"]) ? $_GET["orden"] : "asc";

try {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Inicializar un array para almacenar todos los resultados
        $articulos = [];

        // Verificar si se realiza una búsqueda
        if (isset($_GET['buscar']) && !empty($_GET['busqueda'])) {
            // Si hay una búsqueda
            $sql = "SELECT codigo, nombre, descripcion, categoria, precio, imagen 
                    FROM articulos 
                    WHERE activo = 1 AND (nombre LIKE :busqueda OR descripcion LIKE :busqueda OR categoria LIKE :busqueda)
                    ORDER BY $ordenarPor $orden
                    LIMIT :inicio, :PAGS";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
            $stmt->bindParam(":PAGS", $PAGS, PDO::PARAM_INT);
    
            $busqueda = '%' . $_GET['busqueda'] . '%';
            $stmt->bindParam(":busqueda", $busqueda, PDO::PARAM_STR);
    
            $stmt->execute();
            $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            // Si no hay búsqueda, mostrar artículos según subcategorías seleccionadas
            foreach ($categoriasPrincipales as $categoriaPrincipal) {
                $nombreCampo = "subcategoria_" . $categoriaPrincipal['id_categoria'];
                if (isset($_GET[$nombreCampo]) && !empty($_GET[$nombreCampo])) {
                    $idSubcategoria = $_GET[$nombreCampo];

                    $queryArticulos = "SELECT * FROM articulos WHERE categoria = :idSubcategoria";
                    $stmtArticulos = $con->prepare($queryArticulos);
                    $stmtArticulos->bindParam(':idSubcategoria', $idSubcategoria, PDO::PARAM_INT);
                    $stmtArticulos->execute();
                    $articulos = array_merge($articulos, $stmtArticulos->fetchAll(PDO::FETCH_ASSOC));
        if (empty($articulos)) {
            echo '<table class="table table-responsive table-bordered table-striped align-middle text-center">';
            echo '<tr><td colspan="7"><p>No hay articulos de esta categoria</p></td></tr>';
            echo '</table>';
        }
    }
}
            // Si no hay búsqueda ni subcategoría seleccionada, mostrar todos los artículos
            if (empty($articulos)) {
                $queryTodosArticulos = "SELECT * FROM articulos WHERE activo = 1 ORDER BY $ordenarPor $orden LIMIT :inicio, :PAGS";
                $stmtTodosArticulos = $con->prepare($queryTodosArticulos);
                $stmtTodosArticulos->bindParam(':inicio', $inicio, PDO::PARAM_INT);
                $stmtTodosArticulos->bindParam(':PAGS', $PAGS, PDO::PARAM_INT);
                $stmtTodosArticulos->execute();
                $articulos = $stmtTodosArticulos->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<main class="container">
    <h2>Selecciona tus artículos</h2>
    <div class="container-fluid">
        <div class="float-end">
        <a class="nav-link text-white" href="VerCarta.php">
        <img src="imgs/carrito.png" alt="Carrito" width="50" height="50">
        <?php
        // Obtener la cantidad de artículos en el carrito
        $cartItemCount = ($cart->total_items() > 0) ? $cart->total_items() : 0;

        // Obtener el precio total de los artículos en el carrito
        $totalPrice = ($cart->total() > 0) ? '€' . number_format($cart->total(), 2) : '€0.00';

        // Mostrar la cantidad de artículos en el carrito
        echo '<span class="badge bg-success">' . $cartItemCount . '</span>';

        // Mostrar el precio total al lado del carrito
        echo '<span class="badge bg-success">' . $totalPrice . '</span>';
        ?>
    </a>
        </div>
    </div>
    <table class="table table-responsive table-bordered table-striped align-middle text-center">
    <?php

    // Imprime la tabla
    if (!empty($articulos)) {
        echo '<table class="table table-responsive table-bordered table-striped align-middle text-center">';
        echo '<caption class="caption-bot">Tabla de compras</caption>';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th scope="col">Codigo</th>';
        echo '<th scope="col">Nombre</th>';
        echo '<th scope="col">Descripcion</th>';
        echo '<th scope="col">Categoria</th>';
        echo '<th scope="col">Precio</th>';
        echo '<th scope="col">Imagen</th>';
        echo '<th scope="col">Comprar</th>';
        echo '</tr>';
        echo '</thead>';

        foreach ($articulos as $fila) {
            echo '<tr>';
            echo '<td>' . $fila['codigo'] . '</td>';
            echo '<td>' . $fila['nombre'] . '</td>';
            echo '<td>' . $fila['descripcion'] . '</td>';
            echo '<td>' . $fila['categoria'] . '</td>';
            echo '<td>' . $fila['precio'] . '</td>';
            echo '<td><img src="' . $fila['imagen'] . '" alt="Imagen" style="max-width: 100px; max-height: 100px;"></td>';
            echo '<td><a class="btn btn-success" href="AccionCarta.php?action=addToCart&id=' . $fila['codigo'] . '">Al carrito</a></td>';
            echo '</tr>';
        }
    echo '</table>';
} else {
    echo '<table class="table table-responsive table-bordered table-striped align-middle text-center">';
    echo '<tr><td colspan="7"><p>Articulo no encontrado</p></td></tr>';
    echo '</table>';
}

        // Separador para cada enlace de paginación
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

</main>

<?php
include "footer.php";
$con = null;
?>
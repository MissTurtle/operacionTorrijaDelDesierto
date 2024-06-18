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
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2>Selecciona tus artículos</h2>
        <div>
            <a class="nav-link text-white d-flex align-items-center" href="VerCarta.php">
                <img src="imgs/carrito.png" alt="Carrito" width="50" height="50">
                <?php
                // Obtener la cantidad de artículos en el carrito
                $cartItemCount = ($cart->total_items() > 0) ? $cart->total_items() : 0;

                // Obtener el precio total de los artículos en el carrito
                $totalPrice = ($cart->total() > 0) ? '€' . number_format($cart->total(), 2) : '€0.00';

                // Mostrar la cantidad de artículos en el carrito
                echo '<span class="badge bg-success ms-2">' . $cartItemCount . '</span>';

                // Mostrar el precio total al lado del carrito
                echo '<span class="badge bg-success ms-2">' . $totalPrice . '</span>';
                ?>
            </a>
        </div>
    </div>

    <div class="row">
    <?php
    // Imprime las tarjetas
    if (!empty($articulos)) {
        foreach ($articulos as $fila) {
            echo '<div class="col-md-3 mb-4">';
            echo '<div class="card h-100">';
            echo '<img src="' . $fila['imagen'] . '" class="card-img-top" alt="Imagen del artículo" style="max-height: 200px;">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $fila['nombre'] . '</h5>';
            echo '<p class="card-text">' . $fila['descripcion'] . '</p>';
            echo '</div>';
            echo '<div class="card-footer d-flex justify-content-between align-items-center">';
            echo '<p class="card-text mt-3">Precio: €' . $fila['precio'] . '</p>';
            echo '<a class="btn btn-success" href="AccionCarta.php?action=addToCart&id=' . $fila['codigo'] . '">';
            echo '<img src="imgs/carrito.png" alt="Añadir al carrito" width="25" height="25">';
            echo '</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">Artículo no encontrado</p>';
    }
    ?>

    </div>

    <?php
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
    <a href="?orden=asc" class="text-decoration-none text-success <?= ($orden == 'asc' ? 'selected' : '') ?>">Nombre Asc | </a>
    <a href="?orden=desc" class="text-decoration-none text-success <?= ($orden == 'desc' ? 'selected' : '') ?>">Nombre Desc</a><br><br>

</main>

<?php
include "footer.php";
$con = null;
?>

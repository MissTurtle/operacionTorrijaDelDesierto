<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="imgs/favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <title>Header</title>
    </head>
    <body>
        <header id="header">
            <nav class="navbar navbar-expand-lg navbar-light shadow bg-black">
                <div class="container d-flex justify-content-between align-items-center">
                    <a class="navbar-brand text-success align-self-center" href="index.php">
                        <img src="imgs/logo.png" alt="" width="200">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav" aria-controls="main_nav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse flex d-lg-flex justify-content-lg-end" id="main_nav">
                        <div class="flex">
                            <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="index.php">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="promociones.php">Promociones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="contacto.php">Contacto</a>
                                </li>
                            </ul>
                        </div>
                        <form class="d-flex" method="GET" action="index.php">
                            <input class="form-control me-2" type="search" aria-label="Search" name="busqueda">
                        <button class="btn btn-outline-success" type="submit" name="buscar">Buscar</button>
                    </form>
                    </div>
                </div>
            </nav>
        </header>
        <div class="row">
                <div class="col-md-2 bg-success text-white">
                <?php
                // Función recursiva para obtener subcategorías de una categoría dada
                function obtenerSubcategorias($idCategoria, $con) {
                    $subcategorias = [];

                    $query = "SELECT id_categoria, nombre FROM categorias WHERE id_super = :idCategoria";
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($categoria = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $categoria['subcategorias'] = obtenerSubcategorias($categoria['id_categoria'], $con);
                        $subcategorias[] = $categoria;
                    }

                    return $subcategorias;
                }

                // Obtener todas las categorías principales (id_super = 0)
                $queryPrincipal = "SELECT id_categoria, nombre FROM categorias WHERE id_super = 0";
                $stmtPrincipal = $con->prepare($queryPrincipal);
                $stmtPrincipal->execute();
                $categoriasPrincipales = $stmtPrincipal->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <h2>Busca por categoria</h2>

                <form method="get" action="index.php" id="filterForm" class="auto-submit">
                    <?php foreach ($categoriasPrincipales as $categoriaPrincipal): ?>
                        <label for="categoria_<?php echo $categoriaPrincipal['id_categoria']; ?>">
                            <?php echo $categoriaPrincipal['nombre']; ?>
                        </label>
                        <select name="subcategoria_<?php echo $categoriaPrincipal['id_categoria']; ?>" id="categoria_<?php echo $categoriaPrincipal['id_categoria']; ?>" class="category-select" onchange="this.form.submit()">
                            <option value="">Selecciona una subcategoría</option>
                            <?php
                            $idCategoriaPrincipal = $categoriaPrincipal['id_categoria'];
                            $subcategorias = obtenerSubcategorias($idCategoriaPrincipal, $con);

                            foreach ($subcategorias as $subcategoria) {
                                echo "<option value=\"{$subcategoria['id_categoria']}\">{$subcategoria['nombre']}</option>";
                            }
                            ?>
                        </select>
                        <br><br>
                    <?php endforeach; ?>
                </form>

                <?php
                // Verificar si se envió el formulario
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    // Inicializar un array para almacenar todos los resultados
                    $articulos = [];

                    foreach ($categoriasPrincipales as $categoriaPrincipal) {
                        $nombreCampo = "subcategoria_" . $categoriaPrincipal['id_categoria'];
                        if (isset($_GET[$nombreCampo])) {
                            $idSubcategoria = $_GET[$nombreCampo];

                            $queryArticulos = "SELECT * FROM articulos WHERE categoria = :idSubcategoria";
                            $stmtArticulos = $con->prepare($queryArticulos);
                            $stmtArticulos->bindParam(':idSubcategoria', $idSubcategoria, PDO::PARAM_INT);
                            $stmtArticulos->execute();

                            $articulos = array_merge($articulos, $stmtArticulos->fetchAll(PDO::FETCH_ASSOC));
                        }
                    }
                }
                ?>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var form = document.getElementById('filterForm');
                    
                    form.addEventListener('submit', function() {
                        form.querySelector('input[type="submit"]').disabled = true;
                    });
                });
                </script>
                </div>
                <div class="col-md-8">

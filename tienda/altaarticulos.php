<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Obtengo los datos del formulario
    $codigo = $_POST["codigo"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];

    $activo = isset($_POST["activo"]) ? 1 : 0;

    //Preparo la imagen para trabajar con ella
    $imagen = $_FILES["imagen"];
    $nomImg = $imagen["name"];
    $pesoImg = $imagen["size"];
    $imgTemp = $imagen["tmp_name"];

    try {
        $formImg = pathinfo($nomImg, PATHINFO_EXTENSION);
        
        //Compruebo que la imagen cumple con las condiciones de extension
        if ($formImg !== "jpg" && $formImg !== "jpeg" && $formImg !== "gif" && $formImg !== "png") {
            throw new Exception("La imagen debe tener extension jpg, jpeg, gif o png.");
        }

        //Compruebo que la imagen cumple con la condicion de peso (300 kb * 1024 bytes en un kb: info de internet)
        if ($pesoImg > 300 * 1024) {
            throw new Exception("La imagen es demasiado pesada.");
        }

        //Compruebo que la imagen cumple con la condicion de tamanyo
        $tam = getimagesize($imgTemp);

        if ($tam[0] > 200 || $tam[1] > 200) {
            throw new Exception("El tamanyo de la imagen no debe ser mayor de 200x200px.");
        }

        //Carpeta para las imagenes
        $directorio = "Descargas/";

        //Genero un nombre unico para la imagen (info de internet, muy recomendable)
        $imgNombre = $directorio . uniqid() . "." . $formImg;

        //Muevo la imagen a la carpeta
        move_uploaded_file($imgTemp, $imgNombre);

        //Consulta preparada
        $sql = "INSERT INTO articulos (codigo, nombre, descripcion, categoria, precio, imagen, activo) 
                VALUES (:codigo, :nombre, :descripcion, :categoria, :precio, :imagen, :activo)";

        $stmt = $con->prepare($sql);

        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":imagen", $imgNombre);
        $stmt->bindParam(":activo", $activo);

        $stmt->execute();

        echo "Articulo insertado correctamente.<br>";
        header("refresh:2;url=menuarticulos.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

include "header.php";
?>

<main class="container">
    <h2>Introduce los datos del nuevo articulo</h2>
    <form name="altart" method="post" enctype="multipart/form-data">
        <div class="mb-3">
        <label for="codigo" class="form-label">Codigo:</label>
        <input type="text" class="form-control" name="codigo" pattern="[A-Za-z]{3}\d{5}" title="Ejemplo: abc12345" required>
        </div>
        <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" class="form-control" name="nombre" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="descripcion" class="form-label">Descripcion:</label>
        <textarea name="descripcion" class="form-control" maxlength="100" required></textarea>
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría:</label>
            <select class="form-select" name="categoria" required>
                <?php
                    // Consulta para obtener las categorías
                    $categoriasQuery = $con->query("SELECT id_categoria, nombre FROM categorias WHERE id_super <> 0");
                    
                    while ($row = $categoriasQuery->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id_categoria']}'>{$row['nombre']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="mb-3">
        <label for="precio" class="form-label">Precio:</label>
        <input type="number" class="form-control" name="precio" required>
        </div>
        <div class="mb-3">
        <label for="imagen" class="form-label">Imagen:</label>
        <input type="file" class="form-control" name="imagen" accept=".jpg, .jpeg, .gif, .png" required>
        </div>
        <div class="mb-3">
        <label for="activo" class="form-check-label">Activo:</label>
        <input type="checkbox" class="form-check-input" name="activo" <?php echo ["activo"] ? "checked" : ""; ?>>
        </div>
        <div class="mb-3">
        <input type="reset" class="btn btn-success text-black" name="Borrar" value="Borrar datos">
        <input type="submit" class="btn btn-success text-black" value="Dar de Alta Articulo"><br><br>
        </div>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
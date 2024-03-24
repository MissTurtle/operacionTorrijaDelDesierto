<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST["codigo"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];
    $activo = isset($_POST["activo"]) ? 1 : 0;

    // Verificar si se ha proporcionado una nueva imagen
    if ($_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
        // Procesar la nueva imagen
        $imagen = $_FILES["imagen"];
        $nomImg = $imagen["name"];
        $pesoImg = $imagen["size"];
        $imgTemp = $imagen["tmp_name"];

        try {
            $formImg = pathinfo($nomImg, PATHINFO_EXTENSION);

            // Comprobar que la imagen cumple con las condiciones de extensión
            if ($formImg !== "jpg" && $formImg !== "jpeg" && $formImg !== "gif" && $formImg !== "png") {
                throw new Exception("La imagen debe tener extensión jpg, jpeg, gif o png.");
            }

            // Comprobar que la imagen cumple con la condición de peso (300 kb * 1024 bytes en un kb)
            if ($pesoImg > 300 * 1024) {
                throw new Exception("La imagen es demasiado pesada.");
            }

            // Carpeta para las imágenes
            $directorio = "Descargas/";

            // Generar un nombre único para la imagen
            $imgNombre = $directorio . uniqid() . "." . $formImg;

            // Mover la imagen a la carpeta
            move_uploaded_file($imgTemp, $imgNombre);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // No se proporcionó una nueva imagen, mantener la imagen existente
        $imgNombre = $_POST["imagen_actual"];
    }

    try {
        // Consulta SQL para actualizar los datos del artículo
        $sql = "UPDATE articulos
                SET nombre = :nombre, descripcion = :descripcion,
                categoria = (SELECT id_categoria FROM categorias WHERE nombre = :categoria), 
                precio = :precio, imagen = :imagen, activo = :activo
                WHERE codigo = :codigo";
        $stmt = $con->prepare($sql);

        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":imagen", $imgNombre); // Usar la variable que contiene el nombre de la imagen
        $stmt->bindParam(":activo", $activo);
        $stmt->execute();

        echo "Articulo actualizado correctamente.";
        header("Location: menuarticulos.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if(isset($_GET["codigo"])) {
    $codigo = $_GET["codigo"];
        
    try {
        //Busco el artículo
        $sql = "SELECT * FROM articulos WHERE codigo = :codigo";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->execute();
        $articulo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: menuarticulos.php");
}

include "header.php";
?>

<main class="container">
    <h2>Introduce los nuevos datos del articulo</h2>
        <form name="ediart" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
            <input type="hidden" class="form-control" name="codigo" value="<?php echo $articulo['codigo']; ?>">
            </div>
            <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo $articulo['nombre']; ?>" maxlength="30" required>
            </div>
            <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion:</label>
            <textarea name="descripcion" maxlength="100" required><?php echo $articulo['descripcion']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoria:</label>
                <select class="form-select" name="categoria" required>
                    <?php
                        // Consulta para obtener todas las categorías con id_super diferente a 0
                        $query = "SELECT * FROM categorias WHERE id_super <> 0";
                        $stmt = $con->prepare($query);
                        $stmt->execute();
                        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Iterar sobre las categorías y generar las opciones del menú desplegable
                        foreach ($categorias as $cat) {
                            $selected = ($cat['nombre'] == $articulo['categoria']) ? 'selected' : '';
                            echo "<option value='{$cat['nombre']}' $selected>{$cat['nombre']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" class="form-control" name="precio" value="<?php echo $articulo['precio']; ?>" required>
            </div>
            <input type="hidden" name="imagen_actual" value="<?php echo $articulo['imagen']; ?>">
            <div class="mb-3">
            <label for="imagen" class="form-label">Imagen: (No seleccionar nada para no cambiar)</label>
            <input type="file" class="form-control" name="imagen" accept=".jpg, .jpeg, .gif, .png">
            </div>
            <div class="mb-3">
            <label for="activo" class="form-check-label">Activo:</label>
            <input type="checkbox" class="form-check-input" name="activo" <?php echo $articulo['activo'] ? 'checked' : ''; ?>>
            </div>
            <div class="mb-3">
            <input type="submit" class="btn btn-success text-black" value="Actualizar Datos"><br><br>
            </div>
        </form>
    </main>

<?php
include "footer.php";
$con = null;
?>
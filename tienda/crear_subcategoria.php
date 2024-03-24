<?php
session_start();
include "conectar_db.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $nombre = $_POST["nombre"];
    $id_super = $_POST["id_super"];
    $activo = 1;

    try {
        // El valor de id_super no necesita verificación
        if ($id_super != 0) {
            // Proceder con la inserción
            $query = "INSERT INTO categorias (nombre, id_super, activo) VALUES (:nombre, :id_super, :activo)";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id_super', $id_super, PDO::PARAM_INT);
            $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
            $stmt->execute();

            echo "Categoría agregada con éxito.";
            header("refresh:2;url=vercategorias.php");
        } else {
            echo "Error: El valor de id_super no puede ser 0.";
        }
    } catch (PDOException $e) {
        echo "Error al agregar la categoría: " . $e->getMessage();
    }
}

include "header.php";
?>

<main class="container">
    <h2>Crear Nueva Categoria</h2>
    <form method="post">
        <label for="nombre">Nombre de la categoría:</label>
        <input type="text" name="nombre" required>

        <label for="id_super">Categoría superior:</label>
        <select name="id_super" required>
            <option value="">Selecciona una categoría superior</option>
            <?php
                // Obtener todas las categorías cuyo id_super sea 0
                $categoriasQuery = "SELECT id_categoria, nombre FROM categorias WHERE id_super = 0";
                $categoriasStmt = $con->prepare($categoriasQuery);
                $categoriasStmt->execute();

                while ($categoria = $categoriasStmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"{$categoria['id_categoria']}\">{$categoria['nombre']}</option>";
                }
            ?>
        </select>

        <button type="submit">Crear Categoria</button>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
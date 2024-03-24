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
    // Asignar valores por defecto
    $id_super = 0;
    $activo = 1;

    try {
        // Preparar la consulta
        $query = "INSERT INTO categorias (nombre, id_super, activo) VALUES (:nombre, :id_super, :activo)";
        $stmt = $con->prepare($query);

        // Vincular parámetros
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':id_super', $id_super, PDO::PARAM_INT);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        echo "Categoría agregada con éxito.";
        header("refresh:2;url=vercategorias.php");
    } catch (PDOException $e) {
        echo "Error al agregar la categoría: " . $e->getMessage();
    }
}

include "header.php";
?>

<main class="container">
    <h2>Crear Nueva Categoria</h2>
    <form method="post">
        <label for="nombre">Nombre de la categoria:</label>
        <input type="text" name="nombre" required>
        
        <button type="submit">Crear Categoria</button>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
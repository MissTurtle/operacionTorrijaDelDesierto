<?php
session_start();
include "funciones.php";

if (!isset($_SESSION["dni"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $direccion = $_POST["direccion"];
    $localidad = $_POST["localidad"];
    $provincia = $_POST["provincia"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $contrasenya = $_POST["contrasenya"];
    $rol = $_POST["rol"];
    $activo = isset($_POST["activo"]) ? 1 : 0;

    try {
        // Consulta SQL para obtener la contraseña actual
        $sql_pass = "SELECT contrasenya FROM clientes WHERE dni = :dni";
        $stmt_pass = $con->prepare($sql_pass);
        $stmt_pass->bindParam(":dni", $dni);
        $stmt_pass->execute();
        $fila_pass = $stmt_pass->fetch(PDO::FETCH_ASSOC);
        $pass_enc = $fila_pass['contrasenya'];

        // Si se proporciona una nueva contraseña, cifrarla
        if (!empty($contrasenya)) {
            $pass_enc = password_hash($contrasenya, PASSWORD_DEFAULT);
        }

        // Consulta SQL para actualizar los datos del cliente
        $sql_update = "UPDATE clientes
                SET nombre = :nombre, direccion = :direccion, localidad = :localidad, provincia = :provincia,
                telefono = :telefono, email = :email, contrasenya = :contrasenya, rol = :rol, activo = :activo
                WHERE dni = :dni";
        $stmt_update = $con->prepare($sql_update);

        $stmt_update->bindParam(":dni", $dni);
        $stmt_update->bindParam(":nombre", $nombre);
        $stmt_update->bindParam(":direccion", $direccion);
        $stmt_update->bindParam(":localidad", $localidad);
        $stmt_update->bindParam(":provincia", $provincia);
        $stmt_update->bindParam(":telefono", $telefono);
        $stmt_update->bindParam(":email", $email);
        $stmt_update->bindParam(":contrasenya", $pass_enc);
        $stmt_update->bindParam(":rol", $rol);
        $stmt_update->bindParam(":activo", $activo);

        $stmt_update->execute();

        header("Location: mostrarusuarios.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_GET["dni"])) {
    $dni = $_GET["dni"];

    try {
        // Busco al cliente
        $sql = "SELECT * FROM clientes WHERE dni = :dni";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(":dni", $dni);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Oculto el campo rol si el DNI que voy a modificar coincide con el de un editor o un usuario (la variable se usa en el formulario, y uso CSS para ocultar)
        $ocultarRol = ($_SESSION["dni"] == $fila["dni"] && $fila["rol"] == "usuario" || $_SESSION["dni"] == $fila["dni"] && $fila["rol"] == "editor");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: mostrarusuarios.php");
}

include "header.php";
?>

<main class="container">
    <h2>Introduce los nuevos datos del cliente</h2>
    <form name="formedi" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
        <input type="hidden" class="form-control" name="dni" value="<?php echo $fila['dni']; ?>">
        </div>
        <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" class="form-control" name="nombre" value="<?php echo $fila['nombre']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="direccion" class="form-label">Direccion:</label>
        <input type="text" class="form-control" name="direccion" value="<?php echo $fila['direccion']; ?>" maxlength="50" required>
        </div>
        <div class="mb-3">
        <label for="localidad" class="form-label">Localidad:</label>
        <input type="text" class="form-control" name="localidad" value="<?php echo $fila['localidad']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="provincia" class="form-label">Provincia:</label>
        <input type="text" class="form-control" name="provincia" value="<?php echo $fila['provincia']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="telefono" class="form-label">Telefono:</label>
        <input type="tel" class="form-control" name="telefono" value="<?php echo $fila['telefono']; ?>" pattern="[0-9]{9}" maxlength="9" required>
        </div>
        <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" name="email" value="<?php echo $fila['email']; ?>" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="contrasenya" class="form-label">Nueva Contraseña:</label>
        <input type="password" class="form-control" name="contrasenya" placeholder="Deja en blanco para no cambiar" maxlength="9">
        </div>
        <div class="mb-3">
            <?php if (!$ocultarRol): ?>
            <label for="rol" class="form-label">Rol:</label>
            <?php endif; ?>
            <select name="rol" required <?php echo $ocultarRol ? 'style="display:none;"' : ''; ?>>
                <option value="administrador" <?php echo ($fila["rol"] == "administrador" && !$ocultarRol) ? "selected" : ""; ?>>Administrador</option>
                <option value="editor" <?php echo ($fila["rol"] == "editor" || $ocultarRol) ? "selected" : ""; ?>>Editor</option>
                <option value="usuario" <?php echo ($fila["rol"] == "usuario" || $ocultarRol) ? "selected" : ""; ?>>Usuario</option>
            </select>
        </div>
        <div class="mb-3">
        <label for="activo" class="form-check-label">Activo:</label>
        <input type="checkbox" class="form-check-input" name="activo" <?php echo $fila["activo"] ? "checked" : ""; ?>>
        </div>
        <input type="submit" class="btn btn-success text-black" name="actualizar" value="Actualizar Datos"><br><br>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
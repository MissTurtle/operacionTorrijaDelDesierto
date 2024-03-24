<?php
session_start();
include "funciones.php";

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

    $funciones = new Funciones();

    //Compruebo que el DNI cumple los requisitos
    if ($funciones->validarDNI($dni)) {
        try {
            //Compruebo si el DNI ya existe
            $stmtComprobar = $con->prepare("SELECT dni FROM clientes WHERE dni = :dni");
            $stmtComprobar->bindParam(":dni", $dni);
            $stmtComprobar->execute();

            if ($stmtComprobar->rowCount() > 0) {
                echo "Error: DNI ya registrado.<br>";
                echo '<a href="nuevoregistro.php">Vuelve a intentarlo con otro DNI.</a>';

            } else {
            //Encripto la contrasenya
            $pass_enc = password_hash($contrasenya, PASSWORD_DEFAULT);

            //Sentencia SQL
            $sql = "INSERT INTO clientes (dni, nombre, direccion, localidad, provincia, telefono, email, contrasenya, rol, activo)
                    VALUES (:dni, :nombre, :direccion, :localidad, :provincia, :telefono, :email, :contrasenya, :rol, :activo)";

            //Consulta prepare
            $stmt = $con->prepare($sql);

            $stmt->bindParam(":dni", $dni);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":direccion", $direccion);
            $stmt->bindParam(":localidad", $localidad);
            $stmt->bindParam(":provincia", $provincia);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":contrasenya", $pass_enc);
            $stmt->bindParam(":rol", $rol);
            $stmt->bindParam(":activo", $activo);

            $stmt->execute();

            echo "Te has registrado correctamente.";

            //Tarda 2 segundos en redireccionar para que de tiempo a ver el mensaje de registro correcto (info sacada de php.net)
            header("refresh:2;url=index.php");
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: DNI no valido.<br>";
    }
}

include "header.php";
?>

<main class="container">
    <h2>Introduce los siguientes datos</h2>
    <form name="nureg" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" class="form-control" name="dni" maxlength="9" required>
        </div>
        <div class="mb-3">    
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="direccion" class="form-label">Direccion</label>
        <input type="text" class="form-control" name="direccion" maxlength="50" required>
        </div>
        <div class="mb-3">
        <label for="localidad" class="form-label">Localidad</label>
        <input type="text" class="form-control" name="localidad" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="provincia" class="form-label">Provincia</label>
        <input type="text" class="form-control" name="provincia" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="telefono" class="form-label">Telefono</label>
        <input type="tel" class="form-control" name="telefono" pattern="[0-9]{9}" maxlength="9" required>
        </div>
        <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" maxlength="30" required>
        </div>
        <div class="mb-3">
        <label for="contrasenya" class="form-label">Contrasenya</label>
        <input type="password" class="form-control" name="contrasenya" maxlength="9" required>
        </div>
        <div class="mb-3">
        <label for="rol" class="form-label">Rol</label>
        <select name="rol" required>
            <option value="usuario" selected>Usuario</option>
        </select>
        <br>
        </div>
        <div class="mb-3">
        <label for="activo" class="form-check-label">Activo:</label>
        <input type="checkbox" class="form-check-input" name="activo" <?php echo isset($_POST["activo"]) && $_POST["activo"] == "1" ? "checked" : "checked"; ?>>
        </div>
        <div class="mb-3">
        <input type="reset" class="btn btn-success text-black" name="Borrar" value="Borrar datos">
        <input type="submit" class="btn btn-success text-black" name="Enviar" value="Enviar datos"><br><br>
        </div>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
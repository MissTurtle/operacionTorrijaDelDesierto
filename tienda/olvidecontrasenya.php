<?php
session_start();
include "funciones.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $email = $_POST["email"];

    $funciones = new Funciones();

    //Comprueba que el DNI cumple los requisitos
    if ($funciones->validarDNI($dni)) {
        try {
            //Consulta prepare
            $stmt = $con->prepare("SELECT dni, contrasenya FROM clientes WHERE dni = :dni AND email = :email");
            $stmt->bindParam(":dni", $dni);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                // Enviar la contrasenya actual al correo electrónico del usuario
                echo "Se ha enviado la contrasenya a tu correo electrónico.<br>";
                echo '<meta http-equiv="refresh" content="2;url=index.php">';
                exit();
            } else {
                echo "Error. DNI o email incorrectos.<br>";
                echo '<meta http-equiv="refresh" content="0;url=olvidecontrasenya.php">';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: DNI no válido.";
    }
}

include "header.php";
?>

<main class="container">
    <h2>Introduce tus datos y te enviaremos la contrasenya a tu correo electrónico</h2>
    <form name="olvideContrasenya" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control"  name="dni" maxlength="9" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control"  name="email" maxlength="30" required>
        </div>
        <div class="mb-3">
            <input type="submit" class="btn btn-success text-black" value="Recuperar Contrasenya"><br><br>
        </div>
    </form>
</main>

<?php
include "footer.php";
$con = null;
?>
<?php
session_start();
include "funciones.php";
include "header.php";
?>

<main class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>Politica de privacidad</h2>

            <p class="text-success">Hola usuario de Pulse Synergy.</p>

            <p class="text-success">Esto es una tienda ficticia para un trabajo de clase. Pero igual, aqui esta nuestra politica de privacidad.</p>

            <h3>Informacion que pedimos</h3>
            <p class="text-success">La informacion que pedimos es solo para que interactues con la pagina, no hace falta que pongas datos reales.</p>
            <p class="text-success">Si te arrepientes de haber dado algun dato, basta con que entres a Usuarios y lo cambies pulsando el icono de Editar. Nada de lo que pusiste anteriormente persistira en ninguna parte.</p>

            <h3>¿Para que usamos tu informacion?</h3>
            <p class="text-success">Para nada...</p>

            <h3>¿Le damos tu informacion a alguien?</h3>
            <p class="text-success">No, es mas, te borrare de la base de datos en cuanto te vea, asi que a la proxima empezaras de nuevo.</p>

            <h3>Seguridad</h3>
            <p class="text-success">De todos modos, que sepas que los demas usuarios no podran acceder a tu informacion.</p>
            <p class="text-success">Lo he programado para que cualquiera que quiera probar la pagina quede registrado como un usuario comun, siendo yo la administradora y la unica con acceso a la informacion de todos.</p>
        </div>
    </div>
</main>

<?php
include "footer.php";
$con = null;
?>
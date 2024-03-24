        </div>
        <div class="col-md-2 bg-success text-white">
            <aside class="container-fluid p-2 m-1">
                <div>
                    <main class="container">
                    <?php
                    // Verifica si el usuario está autenticado
                    if (isset($_SESSION["dni"])) {
                        echo '<h3 class="text-center text-white">¡Hola ' . $_SESSION["cliente"]["nombre"] . '!</h3>';
                        // Muestra el menú solo si el usuario está autenticado
                        echo '
                        <div>
                        <nav class="float-end">
                        <ul class="list-unstyled">
                        <li><a  class="nav-link text-decoration-none text-black" href="index.php">Tienda</a></li><br>
                        <li><a  class="nav-link text-decoration-none text-black" href="mostrarusuarios.php">Usuarios</a></li><br>
                        <li><a  class="nav-link text-decoration-none text-black" href="menuarticulos.php">Articulos</a></li><br>
                        <li><a  class="nav-link text-decoration-none text-black" href="mostrarpedidos.php">Pedidos</a></li><br>
                        <li><a  class="nav-link text-decoration-none text-black" href="vercategorias.php">Categorias</a></li><br>
                        <li><a  class="nav-link text-decoration-none text-white" href="logout.php">Cierra sesion</a></li><br>
                        </ul>
                        </nav>
                        </div>';
                    } else {
                        // Muestra el formulario de inicio de sesión si el usuario no está autenticado
                        echo '
                        <h2>Inicia sesion</h2>
                        <form name="log" method="POST" enctype="multipart/form-data" action="login.php">
                        <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" class="form-control" name="dni" maxlength="9" required>
                        </div>
                        <div class="mb-3">
                        <label for="contrasenya" class="form-label">Contrasenya</label>
                        <input type="password" class="form-control" name="contrasenya" maxlength="9" required>
                        </div>
                        <div class="mb-3">
                        <input type="submit" class="btn btn-dark text-success" value="Iniciar Sesion"><br><br>
                        </div>
                        </form>
                        <a class="text-decoration-none text-black" href="nuevoregistro.php">Registrate ahora</a><br><br>
                        <a class="text-decoration-none text-black" href="olvidecontrasenya.php">He olvidado mi contraseña</a>';
                    }
                    ?>
                    </main>
                </div>
            </aside>
        </div>
        <footer class="bg-dark" id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 pt-5">
                        <h2 class="text-success pb-3 border-light bg-dark">PulseSynergy</h2>
                    </div>
                    <div class="col-md-4 pt-5">
                        <h2 class="pb-3 text-light border-bottom border-light bg-dark">Productos</h2>
                            <ul class="list-unstyled">
                                <li class="text-light">Monitores</li>
                                <li class="text-light">Ratones</li>
                                <li class="text-light">Teclados</li>
                                <li class="text-light">Tarjetas Gráficas</li>
                                <li class="text-light">Sistemas Operativos</li>
                                <li class="text-light">Sillas</li>
                                <li class="text-light">Escritorios</li>
                                <li class="text-light">Y Mucho Mas...</li>
                            </ul>
                    </div>
                    <div class="col-md-4 pt-5">
                        <h2 class="pb-3 text-light border-bottom border-light bg-dark">Empresa</h2>
                            <ul class="list-unstyled">
                                <li><a class="text-decoration-none text-light" href="index.php">Inicio</a></li>
                                <li><a class="text-decoration-none text-light" href="quienessomos.php">Quienes somos</a></li>
                                <li><a class="text-decoration-none text-light" href="politicadepriv.php">Política de privacidad</a></li>
                                <li><a class="text-decoration-none text-light" href="contacto.php">Contacto</a></li>
                            </ul>
                    </div>
                </div>
            </div>
            <div class="w-100 bg-black py-2">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                        <p class="text-left text-light bg-black">
                        Copyright &copy; 2024 PulseSynergy</a>
                        </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
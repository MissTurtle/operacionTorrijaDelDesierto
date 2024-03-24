<?php
include "conectar_db.php";

class Funciones {
    private $con;

    public function __construct() {
        global $con;
        $this->con = $con;
    }
    
    //Creo la funcion que creara el array de clientes que usare para buscar clientes
    public function obtenerClientes() {
        $clientes = array();
        
        //Sentencia SQL para coger todos los datos de todos los clientes
        try {
            $sql = "SELECT * FROM clientes";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            //Relleno el array
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $cliente = new Cliente(
                    $fila["dni"],
                    $fila["nombre"],
                    $fila["direccion"],
                    $fila["localidad"],
                    $fila["provincia"],
                    $fila["telefono"],
                    $fila["email"],
                    $fila["contrasenya"],
                    $fila["rol"],
                    $fila["activo"]
                );
                $clientes[] = $cliente;
            }
        } catch (PDOException $e) {
            die("Error al obtener cliente: " . $e->getMessage());
        }
        return $clientes;
    }

    public function validarDNI($dni) {
        //Elimino los espacios en blanco
        $dni = trim($dni);
    
        if (preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
            //Almaceno los 8 numeros en una variable
            $num = substr($dni, 0, 8);
            //Almaceno la letra en una variable, convertida en mayuscula
            $let = strtoupper(substr($dni, 8, 1));
            //Creo un array con las letras que son validas para saber si la letra del DNI introducido corresponde al numero (info de internet)
            $letsValidas = ["T", "R", "W", "A", "G", "M", "Y", "F", "P", "D", "X", "B", "N", "J", "Z", "S", "Q", "V", "H", "L", "C", "K", "E"];
            //Bucle para saber si la letra que he sacado coincide con la calculada sobre las 23 letras posibles, y para convertirla en mayuscula
            if ($let == $letsValidas[$num % 23]) {
                return strtoupper($dni);
            }
        }
    }
}
?>
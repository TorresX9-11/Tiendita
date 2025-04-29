<?php
// Configuración de la conexión
$servidor = "localhost";
$usuario = "root";
$password = ""; // Por defecto en XAMPP no tiene contraseña
$basedatos = "tienda_frutas";

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar el conjunto de caracteres
$conexion->set_charset("utf8");

// Función para limpiar datos de entrada
function limpiarDatos($datos) {
    global $conexion;
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $conexion->real_escape_string($datos);
}
?>
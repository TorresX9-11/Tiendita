<?php
// Incluir archivo de conexión
require_once 'conexion.php';

// Establecer cabeceras para permitir CORS y establecer el tipo de contenido
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Obtener el método HTTP y la ruta
$metodo = $_SERVER['REQUEST_METHOD'];
$ruta = isset($_GET['ruta']) ? $_GET['ruta'] : '';

// Gestionar rutas y métodos
switch ($metodo) {
    case 'GET':
        if (empty($ruta)) {
            // Listar todas las frutas
            obtenerFrutas();
        } else {
            // Obtener una fruta específica por ID
            $id = intval($ruta);
            obtenerFruta($id);
        }
        break;
        
    case 'POST':
        // Agregar nueva fruta
        agregarFruta();
        break;
        
    case 'PUT':
        // Actualizar fruta existente
        $id = intval($ruta);
        actualizarFruta($id);
        break;
        
    case 'DELETE':
        // Eliminar fruta
        $id = intval($ruta);
        eliminarFruta($id);
        break;
        
    default:
        // Método no permitido
        http_response_code(405);
        echo json_encode(array("mensaje" => "Método no permitido"));
}

// Funciones de API

function obtenerFrutas() {
    global $conexion;
    
    // Consulta SQL
    $sql = "SELECT * FROM frutas ORDER BY nombre ASC";
    $resultado = $conexion->query($sql);
    
    if ($resultado) {
        $frutas = array();
        
        while ($fila = $resultado->fetch_assoc()) {
            $frutas[] = $fila;
        }
        
        // Devolver datos
        http_response_code(200);
        echo json_encode($frutas);
    } else {
        // Error en la consulta
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al obtener frutas: " . $conexion->error));
    }
}

function obtenerFruta($id) {
    global $conexion;
    
    // Validar ID
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "ID no válido"));
        return;
    }
    
    // Consulta SQL
    $sql = "SELECT * FROM frutas WHERE id = $id";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $fruta = $resultado->fetch_assoc();
        
        // Devolver datos
        http_response_code(200);
        echo json_encode($fruta);
    } else {
        // No encontrado
        http_response_code(404);
        echo json_encode(array("mensaje" => "Fruta no encontrada"));
    }
}

function agregarFruta() {
    global $conexion;
    
    // Obtener datos POST
    $datos = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos recibidos
    if (!isset($datos['nombre']) || !isset($datos['precio'])) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se proporcionaron los datos necesarios"));
        return;
    }
    
    // Limpiar datos
    $nombre = limpiarDatos($datos['nombre']);
    $descripcion = isset($datos['descripcion']) ? limpiarDatos($datos['descripcion']) : '';
    $precio = floatval($datos['precio']);
    $stock = isset($datos['stock']) ? intval($datos['stock']) : 0;
    
    // Consulta SQL
    $sql = "INSERT INTO frutas (nombre, descripcion, precio, stock) 
            VALUES ('$nombre', '$descripcion', $precio, $stock)";
    
    if ($conexion->query($sql) === TRUE) {
        $id = $conexion->insert_id;
        
        // Devolver respuesta exitosa
        http_response_code(201);
        echo json_encode(array(
            "mensaje" => "Fruta creada correctamente",
            "id" => $id
        ));
    } else {
        // Error en la consulta
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al agregar fruta: " . $conexion->error));
    }
}

function actualizarFruta($id) {
    global $conexion;
    
    // Validar ID
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "ID no válido"));
        return;
    }
    
    // Obtener datos PUT
    $datos = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos recibidos
    if (empty($datos)) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se proporcionaron datos para actualizar"));
        return;
    }
    
    // Construir consulta SQL dinámica
    $actualizaciones = array();
    
    if (isset($datos['nombre'])) {
        $nombre = limpiarDatos($datos['nombre']);
        $actualizaciones[] = "nombre = '$nombre'";
    }
    
    if (isset($datos['descripcion'])) {
        $descripcion = limpiarDatos($datos['descripcion']);
        $actualizaciones[] = "descripcion = '$descripcion'";
    }
    
    if (isset($datos['precio'])) {
        $precio = floatval($datos['precio']);
        $actualizaciones[] = "precio = $precio";
    }
    
    if (isset($datos['stock'])) {
        $stock = intval($datos['stock']);
        $actualizaciones[] = "stock = $stock";
    }
    
    // Si no hay nada que actualizar
    if (empty($actualizaciones)) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se proporcionaron campos válidos para actualizar"));
        return;
    }
    
    // Consulta SQL
    $sql = "UPDATE frutas SET " . implode(", ", $actualizaciones) . " WHERE id = $id";
    
    if ($conexion->query($sql) === TRUE) {
        // Verificar si se actualizó alguna fila
        if ($conexion->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(array("mensaje" => "Fruta actualizada correctamente"));
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "No se encontró la fruta o no se realizaron cambios"));
        }
    } else {
        // Error en la consulta
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al actualizar fruta: " . $conexion->error));
    }
}

function eliminarFruta($id) {
    global $conexion;
    
    // Validar ID
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "ID no válido"));
        return;
    }
    
    // Consulta SQL
    $sql = "DELETE FROM frutas WHERE id = $id";
    
    if ($conexion->query($sql) === TRUE) {
        // Verificar si se eliminó alguna fila
        if ($conexion->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(array("mensaje" => "Fruta eliminada correctamente"));
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "No se encontró la fruta"));
        }
    } else {
        // Error en la consulta
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al eliminar fruta: " . $conexion->error));
    }
}

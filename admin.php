<?php
// Incluir archivo de conexión
require_once 'conexion.php';

// Manejo de formulario para agregar o editar fruta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si es una edición o nueva fruta
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Editar fruta existente
        $id = limpiarDatos($_POST['id']);
        $nombre = limpiarDatos($_POST['nombre']);
        $descripcion = limpiarDatos($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);

        $sql = "UPDATE frutas SET 
                nombre = '$nombre', 
                descripcion = '$descripcion', 
                precio = $precio, 
                stock = $stock 
                WHERE id = $id";

        if ($conexion->query($sql) === TRUE) {
            $mensaje = "Fruta actualizada correctamente";
        } else {
            $error = "Error al actualizar: " . $conexion->error;
        }
    } else {
        // Agregar nueva fruta
        $nombre = limpiarDatos($_POST['nombre']);
        $descripcion = limpiarDatos($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);

        $sql = "INSERT INTO frutas (nombre, descripcion, precio, stock) 
                VALUES ('$nombre', '$descripcion', $precio, $stock)";

        if ($conexion->query($sql) === TRUE) {
            $nuevoId = $conexion->insert_id;
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header("Location: admin.php?mensaje=Fruta agregada correctamente&id=$nuevoId");
            exit();
        } else {
            $error = "Error al agregar: " . $conexion->error;
        }
    }
}

// Eliminar fruta
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM frutas WHERE id = $id";
    
    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Fruta eliminada correctamente";
    } else {
        $error = "Error al eliminar: " . $conexion->error;
    }
}

// Cargar fruta para editar
$fruta_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $sql = "SELECT * FROM frutas WHERE id = $id";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $fruta_editar = $resultado->fetch_assoc();
    }
}

// Obtener todas las frutas
$sql = "SELECT * FROM frutas ORDER BY nombre ASC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Tienda de Frutas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="contenedor">
            <h1>Administración de Frutas</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="admin.php" class="activo">Administración</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="contenedor">
        <?php if (isset($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>

        <section class="admin-form">
            <h2><?php echo $fruta_editar ? 'Editar Fruta' : 'Agregar Nueva Fruta'; ?></h2>
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <?php if ($fruta_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $fruta_editar['id']; ?>">
                <?php endif; ?>

                <div class="form-grupo">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?php echo $fruta_editar ? $fruta_editar['nombre'] : ''; ?>">
                </div>

                <div class="form-grupo">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo $fruta_editar ? $fruta_editar['descripcion'] : ''; ?></textarea>
                </div>

                <div class="form-grupo">
                    <label for="precio">Precio (€):</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required
                           value="<?php echo $fruta_editar ? $fruta_editar['precio'] : ''; ?>">
                </div>

                <div class="form-grupo">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required
                           value="<?php echo $fruta_editar ? $fruta_editar['stock'] : '0'; ?>">
                </div>

                <div class="form-grupo">
                    <button type="submit" class="btn-primary">
                        <?php echo $fruta_editar ? 'Actualizar Fruta' : 'Agregar Fruta'; ?>
                    </button>
                    <?php if ($fruta_editar): ?>
                        <a href="admin.php" class="btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="admin-lista">
            <h2>Lista de Frutas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio (€)</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($fruta = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo isset($fruta['id']) ? $fruta['id'] : ''; ?></td>
                                <td><?php echo isset($fruta['nombre']) ? $fruta['nombre'] : ''; ?></td>
                                <td><?php echo isset($fruta['descripcion']) ? substr($fruta['descripcion'], 0, 50) . (strlen($fruta['descripcion']) > 50 ? '...' : '') : ''; ?></td>
                                <td><?php echo isset($fruta['precio']) ? number_format($fruta['precio'], 2) : ''; ?></td>
                                <td><?php echo isset($fruta['stock']) ? $fruta['stock'] : ''; ?></td>
                                <td>
                                    <?php if (isset($fruta['id'])): ?>
                                        <a href="admin.php?editar=<?php echo $fruta['id']; ?>" class="btn-editar">Editar</a>
                                        <a href="admin.php?eliminar=<?php echo $fruta['id']; ?>" class="btn-eliminar" 
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta fruta?')">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No hay frutas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <div class="contenedor">
            <p>&copy; <?php echo date('Y'); ?> Tienda de Frutas. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="admin.js"></script>
</body>
</html>

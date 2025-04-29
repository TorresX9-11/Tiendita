<?php
// Incluir archivo de conexión
require_once 'conexion.php';

// Obtener todas las frutas
$sql = "SELECT * FROM frutas ORDER BY nombre ASC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Frutas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="contenedor">
            <h1>Tienda de Frutas</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="admin.php">Administración</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="contenedor">
        <section class="banner">
            <h2>Las mejores frutas frescas</h2>
            <p>Calidad garantizada directamente de productores locales</p>
        </section>

        <section class="productos">
            <h2>Nuestras Frutas</h2>
            <div class="grid-productos">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($fruta = $resultado->fetch_assoc()): ?>
                        <div class="producto">
                            <div class="producto-imagen">
                                <?php if (!empty($fruta['imagen'])): ?>
                                    <img src="imagenes/<?php echo $fruta['imagen']; ?>" alt="<?php echo $fruta['nombre']; ?>">
                                <?php else: ?>
                                    <img src="imagenes/default.png" alt="Imagen no disponible">
                                <?php endif; ?>
                            </div>
                            <div class="producto-info">
                                <h3><?php echo $fruta['nombre']; ?></h3>
                                <p class="descripcion"><?php echo $fruta['descripcion']; ?></p>
                                <p class="precio">€<?php echo number_format($fruta['precio'], 2); ?></p>
                                <button class="btn-agregar" data-id="<?php echo $fruta['id']; ?>">Agregar al carrito</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay frutas disponibles en este momento.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="contenedor">
            <p>&copy; <?php echo date('Y'); ?> Tienda de Frutas. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

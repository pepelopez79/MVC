<!DOCTYPE html>
<html>
<head>
    <?php require_once 'cabecera.php'; ?>
</head>
<body class="cuerpo">
<div class="container centrar">
    <div>
        <a href="index.php?accion=listado">Volver</a>
        <a href="index.php?accion=cerrarSesion" style="float: right;">Cerrar sesión</a>
    </div>
    <div class="container cuerpo text-center centrar">
        <p><h2>Detalle de Entrada</h2></p>
    </div>

    <?php foreach ($parametros["mensajes"] as $mensaje): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>" role="alert">
        <!-- Mostramos los mensajes de alerta -->
        <?= $mensaje['mensaje'] ?> 
        </div>
    <?php endforeach; ?>

    <div class="table-container">
        <table class="table" style="width: 70%; float: left; margin-top: 50px">
            <tr>
                <th>ID</th>
                <td><?= $parametros["entrada"]["IDENT"]; ?></td>
            </tr>
            <tr>
                <th>Nick Usuario</th>
                <td><?= $parametros["entrada"]["NICK"]; ?></td>
            </tr>
            <tr>
                <th>Título</th>
                <td><?= $parametros["entrada"]["TITULO"]; ?></td>
            </tr>
            <tr>
                <th>Descripción</th>
                <td><?= $parametros["entrada"]["DESCRIPCION"]; ?></td>
            </tr>
            <tr>
                <th>Nombre Categoría</th>
                <td><?= $parametros["entrada"]["NOMBRECAT"]; ?></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td><?= $parametros["entrada"]["FECHA"]; ?></td>
            </tr>
        </table>
        <?php if ($parametros["entrada"]["IMAGEN"] !== NULL) : ?>
            <div class="image-container" style="float: right; width: 30%;">
                <img src="imagenes/<?= $parametros["entrada"]["IMAGEN"] ?>" alt="Imagen" class="avatar-img" style="border-radius: 3%; width: 100%; height: auto; margin-left: 50px; margin-top: 50px">
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

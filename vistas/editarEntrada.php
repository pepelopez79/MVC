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
        <div class="container cuerpo text-center">
            <h2>Actualizar Entrada</h2>
        </div>

        <?php foreach ($parametros["mensajes"] as $mensaje): ?>
            <!-- Mostramos los mensajes de alerta -->
            <div class="alert alert-<?= $mensaje['tipo'] ?>" role="alert">
                <?= $mensaje['mensaje'] ?>
            </div>
        <?php endforeach; ?>

        <form action="index.php?accion=editarEntrada" method="post" enctype="multipart/form-data">
            <!-- Campo oculto para almacenar el ID de la entrada -->
            <input type="hidden" name="idEntrada" value="<?= $parametros['entrada']['IDENT']; ?>">
            <div class="form-group">
                <label for="nuevaCategoria">Nueva Categoría:</label>
                <!-- Select para elegir la nueva categoría -->
                <select class="form-control" id="nuevaCategoria" name="nuevaCategoria" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <?php $idCategoria = $categoria['IDCAT']; ?>
                        <?php $nombreCategoria = $categoria['NOMBRECAT']; ?>
                        <?php $selected = ($parametros['entrada']['IDCATEGORIA'] == $idCategoria) ? 'selected' : ''; ?>
                        <option value="<?= $idCategoria ?>" <?= $selected ?>><?= $nombreCategoria ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nuevoTitulo">Nuevo Título:</label>
                <input type="text" class="form-control" id="nuevoTitulo" name="nuevoTitulo" value="<?= $parametros['entrada']['TITULO']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nuevaImagen">Nueva Imagen:</label>
                <input type="file" class="form-control" id="nuevaImagen" name="nuevaImagen" accept="image/*">
                <?php 
                    // Variable para almacenar la ruta de la nueva imagen
                    $nuevaImagen = isset($_FILES["nuevaImagen"]) && ($_FILES["nuevaImagen"]["error"] == UPLOAD_ERR_OK) ? "imagenes/" . time() . "-" . $_FILES["nuevaImagen"]["name"] : $parametros["entrada"]["IMAGEN"]; 
                ?>
            </div>
            <div class="form-group">
                <label for="nuevaDescripcion">Nueva Descripción:</label>
                <textarea id="editor" name="nuevaDescripcion" style="width: 100%; min-height: 100px; max-height: 300px;"><?= $parametros['entrada']['DESCRIPCION']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px; cursor: pointer;">Guardar Cambios</button>
        </form>
    </div>
    <script>
        // CKEditor
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>

<?php

// Incluimos el modelo para interactuar con la base de datos
require_once 'modelos/modelo.php';

class controlador {
    private $modelo;
    private $mensajes;
  
    public function __construct() {
        // Inicializamos el modelo y el array de mensajes
        $this->modelo = new modelo();
        $this->mensajes = [];
    }

    // Método para mostrar la página principal
    public function index() {
        session_start();

        // Verificamos si el usuario ha iniciado sesión
        if (!isset($_SESSION['perfil'])) {
            // Redirige a la página de inicio de sesión si el usuario no ha iniciado sesión
            header('Location: index.php?accion=iniciarSesion');
            exit();
        }

        $parametros = [
            "tituloventana" => "Gestión Entradas"
        ];

        // Incluimos la vista de inicio
        include_once 'vistas/inicio.php';
    }

    // Método para iniciar sesión
    public function iniciarSesion() {
        // Verificamos si se ha enviado un formulario de inicio de sesión
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario = $_POST["usuario"];
            $contrasenia = $_POST["contrasenia"];
    
            // Intentamos iniciar sesión con los datos proporcionados
            $resultado = $this->modelo->iniciarSesion($usuario, $contrasenia);
    
            if ($resultado) {
                // Iniciamos la sesión y establecemos las variables de sesión
                session_start();
                $_SESSION["perfil"] = $resultado["ROL"];
                $_SESSION["usuario"] = $resultado["NICK"];
                setcookie("id_usuario", $resultado["IDUSER"], time() + (86400 * 30), "/");
                
                // Verificamos si se seleccionó la opción de recordar usuario y contraseña
                if (isset($_POST['recuerdo']) && $_POST['recuerdo'] == 'on') {
                    // Guardamos las credenciales en cookies
                    setcookie("usuario", $usuario, time() + (86400 * 30), "/");
                    setcookie("contrasenia", $contrasenia, time() + (86400 * 30), "/");
                    setcookie("recuerdo", true, time() + (86400 * 30), "/");
                } else {
                    // Borramos las cookies de recordar usuario y contraseña
                    setcookie("usuario", "", time() - 3600, "/");
                    setcookie("contrasenia", "", time() - 3600, "/");
                    setcookie("recuerdo", "", time() - 3600, "/");
                }
    
                // Redirigimos a la página principal después de iniciar sesión
                header('Location: index.php');
                exit();
            } else {
                // Redirigimos a la página de inicio de sesión con un mensaje de error si las credenciales son incorrectas
                header('Location: index.php?accion=iniciarSesion&error=credenciales');
                exit();
            }
        } else {
            // Si no se envió un formulario, mostramos la página de inicio de sesión
            $usuario = isset($_COOKIE['usuario']) ? $_COOKIE['usuario'] : '';
            $contrasenia = isset($_COOKIE['contrasenia']) ? $_COOKIE['contrasenia'] : '';

            // Parámetros para la vista de inicio de sesión
            $parametros = [
                "tituloventana" => "Iniciar Sesión",
                "mensajes" => $this->mensajes,
            ];
    
            // Incluimos la vista de inicio de sesión
            include_once 'vistas/iniciarSesion.php';
        }
    }

    // Método para cerrar sesión
    public function cerrarSesion() {
        session_start();
        
        // Borramos todas las variables de sesión
        $_SESSION = array();
    
        // Destruimos la sesión
        session_destroy();
    
        // Redirigimos a la página de inicio de sesión después de cerrar sesión
        header('Location: index.php?accion=iniciarSesion');
        exit();
    }    
    
    // Método para mostrar el listado de entradas
    public function listado() {
        session_start();
    
        // Verificamos si el usuario ha iniciado sesión
        if (!isset($_SESSION['perfil'])) {
            // Redirigimos a la página de inicio de sesión si el usuario no ha iniciado sesión
            header('Location: index.php?accion=iniciarSesion&error=fuera');
            exit();
        }
    
        // Obtenemos los parámetros de orden, página y registros por página
        $orden = isset($_GET['orden']) && in_array($_GET['orden'], ['asc', 'desc']) ? $_GET['orden'] : 'asc';
        $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
        $regsxpag = isset($_GET['regsxpag']) ? intval($_GET['regsxpag']) : 5;
    
        // Obtenemos el listado de entradas según el perfil del usuario
        if ($_SESSION['perfil'] == 'admin') {
            $resultModelo = $this->modelo->listadoPaginado($orden, $regsxpag, $pagina);
        } elseif ($_SESSION['perfil'] == 'user') {
            $idUsuario = $_COOKIE['id_usuario'];
            $resultModelo = $this->modelo->listadoPorUsuarioPaginado($idUsuario, $orden, $regsxpag, $pagina);
        }
    
        $parametros = [
            "tituloventana" => "Listar Entradas",
            "datos" => NULL,
            "mensajes" => [],
            "orden" => $orden,
            "pagina" => $pagina,
            "regsxpag" => $regsxpag,
            "totalPaginas" => 0
        ];
    
        // Verificamos si se obtuvo correctamente el listado de entradas
        if ($resultModelo["correcto"]) {
            // Asignamos los datos y el total de páginas obtenidos del modelo a los parámetros
            $parametros["datos"] = $resultModelo["datos"];
            $parametros["totalPaginas"] = $resultModelo["totalPaginas"];

            $this->mensajes[] = [
                "tipo" => "success",
                "mensaje" => "El listado se realizó correctamente"
            ];
        } else {
            // Agrega un mensaje de error al array de mensajes si no se pudo obtener el listado de entradas
            $this->mensajes[] = [
                "tipo" => "danger",
                "mensaje" => "El listado no pudo realizarse correctamente<br/>({$resultModelo["error"]})"
            ];
        }
    
        // Incluimos la vista de listado de entradas
        include_once 'vistas/listado.php';
    }    
    
    // Método para añadir una nueva entrada
    public function anadirEntrada() {
        $errores = array();
        
        // Obtenemos las categorías para el formulario de añadir entrada
        $categorias = $this->modelo->obtenerCategorias();
    
        // Verificamos si se ha enviado un formulario de añadir entrada
        if (isset($_POST) && !empty($_POST) && isset($_POST['submit'])) {
            $idusuario = $_POST['idusuario'];
            $idcategoria = $_POST['categoria'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $imagen = NULL;

            // Verificamos si se ha adjuntado una imagen
            if (isset($_FILES["imagen"]) && (!empty($_FILES["imagen"]["tmp_name"]))) {
                // Verificamos si existe el directorio de imágenes, si no, lo creamos
                if (!is_dir("imagenes")) {
                    $dir = mkdir("imagenes", 0777, true);
                } else {
                    $dir = true;
                }
                if ($dir) {
                    // Generamos un nombre único para la imagen y la movemos al directorio de imágenes
                    $nombreImagen = time() . "-" . $_FILES["imagen"]["name"];
                    $movimiento = move_uploaded_file($_FILES["imagen"]["tmp_name"], "imagenes/" . $nombreImagen);
                    if ($movimiento) {
                        $imagen = $nombreImagen;
                    } else {
                        $this->mensajes[] = [
                            "tipo" => "danger",
                            "mensaje" => "Error: La imagen no se pudo cargar correctamente."
                        ];
                        $errores["imagen"] = "Error: La imagen no se pudo cargar correctamente.";
                    }
                }
            }
    
            // Verificamos si no hay errores de validación
            if (count($errores) == 0) {
                $resultModelo = $this->modelo->anadirEntrada([
                    'idusuario' => $idusuario,
                    'idcategoria' => $idcategoria,
                    'titulo' => $titulo,
                    'imagen' => $imagen,
                    'descripcion' => $descripcion
                ]);
                // Verificamos si se añadió correctamente la entrada
                if ($resultModelo["correcto"]) {
                    $this->mensajes[] = [
                        "tipo" => "success",
                        "mensaje" => "La entrada se ha agregado correctamente."
                    ];
                } else {
                    $this->mensajes[] = [
                        "tipo" => "danger",
                        "mensaje" => "La entrada no se ha podido añadir correctamente." . $resultModelo["error"]
                    ];
                }
            } else {
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => "Error en los datos de la entrada."
                ];
            }
        }
    
        $parametros = [
            "tituloventana" => "Agregar Entrada",
            "mensajes" => $this->mensajes,
            "categorias" => $categorias
        ];

        // Incluimos la vista de añadir entrada
        include_once 'vistas/anadirEntrada.php';
    }    

    // Método para editar una entrada
    public function editarEntrada() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtenemos el ID de la entrada a editar
            $idEntrada = $_POST['idEntrada'];
            
            // Obtenemos los detalles de la entrada a editar
            $resultadoEntrada = $this->modelo->mostrarEntrada($idEntrada);
            if (!$resultadoEntrada["correcto"]) {
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => "No se pudo obtener la entrada existente para actualizarla."
                ];
                // Redirigimos al listado de entradas
                $this->listado();
                return;
            }

            // Obtenemos la imagen actual de la entrada
            $imagen = $resultadoEntrada['datos']['IMAGEN'];

            if (isset($_FILES["nuevaImagen"]) && (!empty($_FILES["nuevaImagen"]["tmp_name"]))) {
                if (!is_dir("imagenes")) {
                    $dir = mkdir("imagenes", 0777, true);
                } else {
                    $dir = true;
                }
                if ($dir) {
                    $nombreImagen = time() . "-" . $_FILES["nuevaImagen"]["name"];
                    $movimiento = move_uploaded_file($_FILES["nuevaImagen"]["tmp_name"], "imagenes/" . $nombreImagen);
                    if ($movimiento) {
                        $imagen = $nombreImagen;
                    } else {
                        $this->mensajes[] = [
                            "tipo" => "danger",
                            "mensaje" => "Error: La imagen no se pudo cargar correctamente."
                        ];
                        $errores["nuevaImagen"] = "Error: La imagen no se pudo cargar correctamente.";
                    }
                }
            }       
            
            $datos = [
                "idEntrada" => $_POST['idEntrada'],
                "nuevoTitulo" => $_POST['nuevoTitulo'],
                "nuevaCategoria" => $_POST['nuevaCategoria'],
                "nuevaDescripcion" => $_POST['nuevaDescripcion'],
                "nuevaImagen" => $imagen
            ];
    
            $resultado = $this->modelo->editarEntrada($datos);
    
            if ($resultado["correcto"]) {
                $this->mensajes[] = [
                    "tipo" => "success",
                    "mensaje" => "Entrada actualizada correctamente."
                ];
            } else {
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => $resultado["error"]
                ];
            }
    
            // Redirigimos al listado de entradas
            $this->listado();
        } elseif (isset($_GET['id'])) {
            // Si no se envió un formulario pero se proporcionó un ID de entrada, mostramos el formulario de edición
            $idEntrada = $_GET['id'];
            
            // Obtenemos los detalles de la entrada a editar
            $resultadoEntrada = $this->modelo->mostrarEntrada($idEntrada);
    
            if ($resultadoEntrada["correcto"]) {
                $entrada = $resultadoEntrada["datos"];
                $categorias = $this->modelo->obtenerCategorias();
    
                $parametros = [
                    "tituloventana" => "Editar Entrada",
                    "entrada" => $entrada,
                    "categorias" => $categorias,
                    "mensajes" => []
                ];

                // Incluimos la vista de editar entrada
                include_once 'vistas/editarEntrada.php';
            } else {
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => "Error al obtener los detalles de la entrada."
                ];

                // Redirigimos al listado de entradas
                $this->listado();
            }
        } else {
            $this->mensajes[] = [
                "tipo" => "danger",
                "mensaje" => "ID de entrada no proporcionado."
            ];

            // Redirigimos al listado de entradas
            $this->listado();
        }
    }

    // Método para eliminar una entrada
    public function eliminarEntrada() {
        // Verificamos si se proporcionó un ID de entrada válido
        if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
            // Obtenemos el ID de la entrada a eliminar
            $id = $_GET["id"];
            // Intentamos eliminar la entrada utilizando el modelo
            $resultModelo = $this->modelo->eliminarEntrada($id);
            if ($resultModelo["correcto"]) :
                $this->mensajes[] = [
                    "tipo" => "success",
                    "mensaje" => "Se eliminó correctamente la entrada con ID $id"
                ];
            else :
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => "La eliminación de la entrada no se realizó correctamente<br/>({$resultModelo["error"]})"
                ];
            endif;
        } else {
            $this->mensajes[] = [
                "tipo" => "danger",
                "mensaje" => "No se pudo acceder al ID de la entrada a eliminar"
            ];
        }

        // Redirigimos al listado de entradas
        $this->listado();
    }
    
    // Método para mostrar los detalles de una entrada
    public function mostrarEntrada() {
        if (isset($_GET['id'])) {
            // Obtenemos el ID de la entrada
            $idEntrada = $_GET['id'];
            
            // Obtenemos los detalles de la entrada
            $resultado = $this->modelo->mostrarEntrada($idEntrada);
    
            if ($resultado["correcto"]) {
                $entrada = $resultado["datos"];

                $parametros = [
                    "tituloventana" => "Detalle Entrada",
                    "entrada" => $entrada,
                    "mensajes" => []
                ];

                // Incluimos la vista de mostrar entrada
                include_once 'vistas/mostrarEntrada.php';
            } else {
                $this->mensajes[] = [
                    "tipo" => "danger",
                    "mensaje" => "Error al obtener los detalles de la entrada."
                ];

                // Redirigimos al listado de entradas
                $this->listado();
            }
        } else {
            $this->mensajes[] = [
                "tipo" => "danger",
                "mensaje" => "ID de entrada no proporcionado."
            ];

            // Redirigimos al listado de entradas
            $this->listado();
        }
    }
}
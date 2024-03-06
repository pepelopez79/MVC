<?php

class Modelo {
    private $conexion;
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "bdblog";

    // Constructor para inicializar la conexión a la base de datos
    public function __construct() {
        $this->conectar();
    }

    // Método para establecer la conexión con la base de datos
    public function conectar() {
        try {
            $this->conexion = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return TRUE;
        } catch (PDOException $ex) {
            return $ex->getMessage();
        }
    }

    // Método para iniciar sesión
    public function iniciarSesion($usuario, $contrasenia) {
        try {
            $consulta = $this->conexion->prepare("SELECT * FROM USUARIOS WHERE NICK = :usuario AND CONTRASENIA = :contrasenia");
            $consulta->bindParam(':usuario', $usuario);
            $consulta->bindParam(':contrasenia', $contrasenia);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            return false;
        }
    }

    // Método para obtener el listado de entradas
    public function listado($orden) {
        $return = [
            "correcto" => FALSE,
            "datos" => NULL,
            "error" => NULL
        ];
    
        try {
            // Consulta SQL para obtener las entradas ordenadas por fecha
            $sql = "SELECT e.*, u.NICK AS 'Nick Usuario', c.NOMBRECAT AS 'Nombre Categoría' 
                    FROM entradas e
                    INNER JOIN usuarios u ON e.IDUSUARIO = u.IDUSER
                    INNER JOIN categorias c ON e.IDCATEGORIA = c.IDCAT
                    ORDER BY e.FECHA $orden";
            $resultsquery = $this->conexion->query($sql);

            if ($resultsquery) :
                $return["correcto"] = TRUE;
                $return["datos"] = $resultsquery->fetchAll(PDO::FETCH_ASSOC);
            endif;
        } catch (PDOException $ex) {
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }

    // Método para obtener el listado de entradas por usuario
    public function listadoPorUsuario($idUsuario, $orden) {
        $return = [
            "correcto" => FALSE,
            "datos" => NULL,
            "error" => NULL
        ];
    
        try {
            // Consulta SQL para obtener las entradas de un usuario ordenadas por fecha
            $sql = "SELECT e.*, u.NICK AS 'Nick Usuario', c.NOMBRECAT AS 'Nombre Categoría' 
                    FROM entradas e
                    INNER JOIN usuarios u ON e.IDUSUARIO = u.IDUSER
                    INNER JOIN categorias c ON e.IDCATEGORIA = c.IDCAT
                    WHERE e.IDUSUARIO = :idUsuario
                    ORDER BY e.FECHA $orden";
            $query = $this->conexion->prepare($sql);
            // Ejecutar la consulta con el id de usuario como parámetro
            $query->execute(['idUsuario' => $idUsuario]);

            if ($query) :
                $return["correcto"] = TRUE;
                $return["datos"] = $query->fetchAll(PDO::FETCH_ASSOC);
            endif;
        } catch (PDOException $ex) {
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }

    // Método para obtener el listado de entradas paginado
    public function listadoPaginado($orden, $regsxpag, $pagina) {
        $return = [
            "correcto" => FALSE,
            "datos" => NULL,
            "error" => NULL,
            "totalPaginas" => 0
        ];
    
        try {
            // Consulta SQL para contar el número total de entradas
            $sqlCount = "SELECT COUNT(*) AS total FROM entradas";
            $queryCount = $this->conexion->query($sqlCount);
            $totalRegistros = $queryCount->fetchColumn();
            // Calcular el número total de páginas
            $return["totalPaginas"] = ceil($totalRegistros / $regsxpag);
    
            // Calcular el desplazamiento para la paginación
            $offset = ($pagina - 1) * $regsxpag;
    
            // Consulta SQL para obtener las entradas paginadas
            $sql = "SELECT e.*, u.NICK AS 'Nick Usuario', c.NOMBRECAT AS 'Nombre Categoría' 
                    FROM entradas e
                    INNER JOIN usuarios u ON e.IDUSUARIO = u.IDUSER
                    INNER JOIN categorias c ON e.IDCATEGORIA = c.IDCAT
                    ORDER BY e.FECHA $orden
                    LIMIT $offset, $regsxpag";
            $query = $this->conexion->query($sql);

            if ($query) {
                $return["correcto"] = TRUE;
                $return["datos"] = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $ex) {
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }

    // Método para obtener el listado de entradas por usuario paginado
    public function listadoPorUsuarioPaginado($idUsuario, $orden, $regsxpag, $pagina) {
        $return = [
            "correcto" => FALSE,
            "datos" => NULL,
            "error" => NULL,
            "totalPaginas" => 0
        ];
    
        try {
            // Consulta SQL para contar el número total de entradas de un usuario
            $sqlCount = "SELECT COUNT(*) AS total FROM entradas WHERE IDUSUARIO = :idUsuario";
            $queryCount = $this->conexion->prepare($sqlCount);
            $queryCount->execute(['idUsuario' => $idUsuario]);
            $totalRegistros = $queryCount->fetchColumn();
            // Calcular el número total de páginas
            $return["totalPaginas"] = ceil($totalRegistros / $regsxpag);
    
            // Calcular el desplazamiento para la paginación
            $offset = ($pagina - 1) * $regsxpag;
    
            // Consulta SQL para obtener las entradas de un usuario paginadas
            $sql = "SELECT e.*, u.NICK AS 'Nick Usuario', c.NOMBRECAT AS 'Nombre Categoría' 
                    FROM entradas e
                    INNER JOIN usuarios u ON e.IDUSUARIO = u.IDUSER
                    INNER JOIN categorias c ON e.IDCATEGORIA = c.IDCAT
                    WHERE e.IDUSUARIO = :idUsuario
                    ORDER BY e.FECHA $orden
                    LIMIT $offset, $regsxpag";
            $query = $this->conexion->prepare($sql);
            // Ejecutar la consulta con el id de usuario como parámetro
            $query->execute(['idUsuario' => $idUsuario]);

            if ($query) {
                $return["correcto"] = TRUE;
                $return["datos"] = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $ex) {
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }

    // Método para añadir una nueva entrada
    public function anadirEntrada($datos) {
        $return = [
            "correcto" => FALSE,
            "error" => NULL
        ];
    
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();

            // Consulta SQL para insertar una nueva entrada en la base de datos
            $sql = "INSERT INTO entradas(IDUSUARIO, IDCATEGORIA, TITULO, IMAGEN, DESCRIPCION)
                    VALUES (:idusuario, :idcategoria, :titulo, :imagen, :descripcion)";

            $query = $this->conexion->prepare($sql);

            $query->execute([
                'idusuario' => $datos["idusuario"],
                'idcategoria' => $datos["idcategoria"],
                'titulo' => $datos["titulo"],
                'imagen' => $datos["imagen"],
                'descripcion' => $datos["descripcion"]
            ]); 

            if ($query) {
                // Confirmar transacción
                $this->conexion->commit();
                $return["correcto"] = TRUE;
            }
        } catch (PDOException $ex) {
            // Revertir transacción en caso de error
            $this->conexion->rollback();
        }
    
        return $return;
    }    

    // Método para editar una entrada existente
    public function editarEntrada($datos) {
        $return = [
            "correcto" => FALSE,
            "error" => NULL
        ];
    
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();
    
            // Consulta SQL para actualizar una entrada en la base de datos
            $sql = "UPDATE ENTRADAS SET TITULO = :nuevoTitulo, DESCRIPCION = :nuevaDescripcion, IDCATEGORIA = :nuevaCategoria, IMAGEN = :nuevaImagen WHERE IDENT = :idEntrada";
            $query = $this->conexion->prepare($sql);

            $query->execute([
                'idEntrada' => $datos["idEntrada"],
                'nuevoTitulo' => $datos["nuevoTitulo"],
                'nuevaCategoria' => $datos["nuevaCategoria"],
                'nuevaDescripcion' => $datos["nuevaDescripcion"],
                'nuevaImagen' => $datos["nuevaImagen"]
            ]);
    
            if ($query) {
                // Confirmar transacción
                $this->conexion->commit();
                $return["correcto"] = TRUE;
            } else {
                // Revertir transacción en caso de error
                $this->conexion->rollback();
                $return["error"] = "No se pudo actualizar la entrada.";
            }
        } catch (PDOException $ex) {
            // Revertir transacción en caso de error
            $this->conexion->rollback();
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }    

    // Método para eliminar una entrada
    public function eliminarEntrada($id) {
        $return = [
            "correcto" => FALSE,
            "error" => NULL
        ];
    
        if ($id && is_numeric($id)) {
            try {
                // Iniciar transacción
                $this->conexion->beginTransaction();
                // Consulta SQL para eliminar una entrada de la base de datos
                $sql = "DELETE FROM entradas WHERE IDENT=:id";
                $query = $this->conexion->prepare($sql);
                // Ejecutar la consulta con el id de entrada como parámetro
                $query->execute(['id' => $id]);

                if ($query) {
                    // Confirmar transacción
                    $this->conexion->commit();
                    $return["correcto"] = TRUE;
                }
            } catch (PDOException $ex) {
                // Revertir transacción en caso de error
                $this->conexion->rollback();
                $return["error"] = $ex->getMessage();
            }
        } else {
            $return["correcto"] = FALSE;
        }
    
        return $return;
    }   
    
    // Método para mostrar los detalles de una entrada
    public function mostrarEntrada($idEntrada) {
        $return = [
            "correcto" => FALSE,
            "datos" => NULL,
            "error" => NULL
        ];
    
        try {
            // Consulta SQL para obtener los detalles de una entrada específica
            $sql = "SELECT e.*, u.NICK AS 'NICK', c.NOMBRECAT AS 'NOMBRECAT'
                    FROM ENTRADAS e
                    INNER JOIN USUARIOS u ON e.IDUSUARIO = u.IDUSER
                    INNER JOIN CATEGORIAS c ON e.IDCATEGORIA = c.IDCAT
                    WHERE IDENT = :idEntrada";
            $query = $this->conexion->prepare($sql);
            // Ejecutar la consulta con el id de entrada como parámetro
            $query->execute(['idEntrada' => $idEntrada]);
    
            if ($query) {
                $return["correcto"] = TRUE;
                $return["datos"] = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $ex) {
            $return["error"] = $ex->getMessage();
        }
    
        return $return;
    }

    // Método para obtener todas las categorías
    public function obtenerCategorias() {
        try {
            if (!$this->conexion) {
                $this->conectar();
            }
    
            // Consulta SQL para obtener todas las categorías
            $sql = "SELECT IDCAT, NOMBRECAT FROM CATEGORIAS";
            $query = $this->conexion->query($sql);
    
            // Obtener y devolver las categorías como un array asociativo
            $categorias = $query->fetchAll(PDO::FETCH_ASSOC);
    
            return $categorias;
        } catch (PDOException $ex) {
            return array();
        }
    }
}

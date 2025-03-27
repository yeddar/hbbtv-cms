<?php

class UserLog {
    public int $id_user;
    public string $action;
    public string $promo;
    public int $id_promo;


    // Constructor para inicializar datos
    public function __construct(int $id_user = 0, string $action = "", string $promo = "", int $id_promo = 0) {
        $this->id_user = $id_user;
        $this->action = $action;
        $this->promo = $promo;
        $this->id_promo = $id_promo;
    }


    // Listar todos los logs
    public static function listUserLogs(PDO $pdo, int $offset, $numElems): array {
        try {
            $sql = "SELECT ul.datetime, u.username, ul.action, ul.promo
                    FROM UserLogs ul
                    INNER JOIN Users u ON ul.id_user = u.id
                    ORDER BY ul.datetime DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':limit', $numElems, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

  
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            throw new Exception("Error al listar los logs: " . $e->getMessage());
        }
    }


    public static function getLastUpdateLogByIdPromo(PDO $pdo, int $id_promo) {
        try {
            $sql = "SELECT ul.datetime, u.username
                    FROM UserLogs ul
                    INNER JOIN Users u ON ul.id_user = u.id
                    WHERE ul.id_promo = :id_promo
                    ORDER BY ul.datetime DESC
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':id_promo', $id_promo, PDO::PARAM_INT);
            
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

         
            return $result;

        } catch(PDOException $e) {
            throw new RuntimeException("Error al obtener logs para la promo con ID $id_promo: " . $e->getMessage(), 0, $e);
        }


    }

    // Crear un usuario
    public static function createLog(PDO $pdo, UserLog $log) {
        try {
            $sql = "INSERT INTO UserLogs (id_user, action, promo, id_promo) VALUES (:id_user, :action, :promo, :id_promo)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':id_user' => $log->id_user,
                ':action' => $log->action,
                ':promo' => $log->promo,
                ':id_promo' => $log->id_promo
            ]);

            return true;

        } catch (PDOException $e) {
            throw new Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

   
}

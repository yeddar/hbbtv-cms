<?php

class Promo {
    public ?int $id;
    public ?string $name;
    public ?string $image;
    public ?string $title;
    public ?string $subtitle;
    public ?string $publish_date;
    public ?string $create_time;
    public ?string $update_time;
    public ?string $status;

    // Constructor para inicializar los datos
    public function __construct($id, $name, $image, $title, $subtitle, $publish_date, $create_time, $update_time, $status) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->publish_date = $publish_date;
        $this->create_time = $create_time;
        $this->update_time = $update_time;
        $this->status = $status;
    }


    public static function getLivePromo(PDO $pdo, String $status = 'live') {
        try {
            $sql = "SELECT TOP 1 id, name, image, title, subtitle, publish_date, create_time, update_time, status
                    FROM PromosGanxoHbbtv
                    WHERE status = :status";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null; // Devuelve null si no hay resultados
            }

            return new Promo(
                $result['id'],
                $result['name'],
                $result['image'],
                $result['title'],
                $result['subtitle'],
                $result['publish_date'],
                $result['create_time'], 
                $result['update_time'],
                $result['status']
            );

        } catch(PDOException $e) {
            throw new Exception("Error al intentar obtener la promo activa: " . $e->getMessage());
        }
    }


    public static function getPromoById(PDO $pdo, $idPromo) {
        try {
            $sql = "SELECT id, name, image, title, subtitle, publish_date, create_time, update_time, status
                    FROM PromosGanxoHbbtv
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $idPromo, PDO::PARAM_INT);
            
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null; // Devuelve null si no hay resultados
            }

            return new Promo(
                $result['id'],
                $result['name'],
                $result['image'],
                $result['title'],
                $result['subtitle'],
                $result['publish_date'],
                $result['create_time'], 
                $result['update_time'],
                $result['status']
            );

        } catch(PDOException $e) {
            throw new Exception("Error al obtener la promoción con ID $idPromo: " . $e->getMessage());
        }

    }

    public static function getPromos(PDO $pdo, String $status, int $offset = 0, String $filterByName = "%", int $itemsPerPage = 5) {

        try {
            // Determinar el orden según el status
            $orderBy = ($status === "scheduled") ? "ASC" : "DESC";


            $sql = "SELECT id, name, image, title, subtitle, create_time, update_time, publish_date, status 
            FROM PromosGanxoHbbtv
            WHERE status = :status AND name COLLATE SQL_Latin1_General_CP1_CI_AI LIKE :filterByName
            ORDER BY publish_date $orderBy
            OFFSET :offset ROWS
            FETCH NEXT :itemsPerPage ROWS ONLY";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            
            $filterByName = "%{$filterByName}%"; // Agrego % para buscar coincidencias parciales
            $stmt->bindParam(':filterByName', $filterByName, PDO::PARAM_STR);
           
            $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            // Array para almacenar las promociones
            $promos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Crear un objeto Promo por cada fila y añadirlo al array
                $promos[] = new Promo(
                    $row['id'],
                    $row['name'],
                    $row['image'],
                    $row['title'],
                    $row['subtitle'],
                    $row['publish_date'],
                    $row['create_time'], 
                    $row['update_time'],
                    $row['status']
                );
            }

            return $promos;

        } catch(PDOException $e) {
            throw new Exception("Error al listar promociones: " . $e->getMessage());
        }
    }

    public static function deletePromo(PDO $pdo, $idPromo) {
        try {
            $sql = "DELETE FROM PromosGanxoHbbtv
                WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $idPromo, PDO::PARAM_INT);
            $stmt->execute();
        
            return true;
        
        } catch (PDOException $e) {
            throw new Exception("Error al intentar eliminar la promo: " . $e->getMessage());
        }
        
    }


    public static function createPromo(PDO $pdo, Promo $promo) {
        try {

            $sql = "INSERT INTO PromosGanxoHbbtv (name, image, title, subtitle, publish_date, create_time, update_time, status) 
                    VALUES (:name, :image, :title, :subtitle, :publish_date, :create_time, :update_time, :status)";
            
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':name' => $promo->name,
                ':image' => $promo->image,
                ':title' => $promo->title,
                ':subtitle' => $promo->subtitle,
                ':publish_date' => $promo->publish_date,
                ':create_time' => $promo->create_time, 
                ':update_time' => $promo->update_time, 
                ':status' => $promo->status
            ]);
            
            $idPromo = $pdo->lastInsertId();

            $insertedPromo = self::getPromoById($pdo, $idPromo);
            return $insertedPromo;

        } catch (PDOException $e) {
            throw new Exception("Error al crear la promo: " . $e->getMessage());
        }
    }


    public static function updatePromo(PDO $pdo, Promo $promo) {
        try {
            
            $sql = "UPDATE PromosGanxoHbbtv 
                    SET title = :title, subtitle = :subtitle, publish_date = :publish_date, update_time = :update_time, image = :image
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':title', $promo->title, PDO::PARAM_STR);
            $stmt->bindParam(':subtitle', $promo->subtitle, PDO::PARAM_STR);
            $stmt->bindParam(':publish_date', $promo->publish_date, PDO::PARAM_STR);
            $stmt->bindParam(':update_time', $promo->update_time, PDO::PARAM_STR);
            $stmt->bindParam(':image', $promo->image, PDO::PARAM_STR);

            $stmt->bindParam(':id', $promo->id, PDO::PARAM_INT);
    
            $stmt->execute();
        
            $updatedPromo = self::getPromoById($pdo, $promo->id);
            return $updatedPromo;
    
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la promo: " . $e->getMessage());
        }
    }

}


?>
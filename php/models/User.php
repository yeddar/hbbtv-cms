<?php

class User {
    public int $id;
    public string $username;
    public string $pass;

    // Constructor para inicializar datos
    public function __construct($id, $username, $pass) {
        $this->id = $id;
        $this->username = $username;
        $this->pass = $pass;
    }

    // Para verificar la autenticación de un usuario
    public static function authenticate($pdo, $username, $password) {
        try {
            // SQL para verificar las credenciales del usuario
            $sql = "SELECT * FROM Users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verificar que la contraseña es correcta
                if (password_verify($password, $user['pass'])) {
                    return new User($user['id'], $user['username'], $user['pass']); // Devuelve el objeto 
                }
            }

            return null; // Autenticación fallida

        } catch (PDOException $e) {
            throw new Exception("Error al autenticar el usuario: " . $e->getMessage());
        }
    }

    // Listar todos los usuarios
    public static function listUsers(PDO $pdo): array {
        try {
            $sql = "SELECT id, username, pass FROM Users";
            $stmt = $pdo->query($sql);

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new User($row['id'], $row['username'], $row['pass']);
            }

            return $users;

        } catch (PDOException $e) {
            throw new Exception("Error al listar usuarios: " . $e->getMessage());
        }
    }

    // Crear un usuario
    public static function createUser(PDO $pdo, User $user) {
        try {
            $sql = "INSERT INTO Users (username, pass) VALUES (:username, :pass)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':username' => $user->username,
                ':pass' => password_hash($user->pass, PASSWORD_BCRYPT) // Encriptar contraseña
            ]);

            return true;

        } catch (PDOException $e) {
            throw new Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

    // Actualizar un usuario
    public static function updateUser($pdo, $username, $data) {
        try {
            $sql = "UPDATE Users SET pass = :pass WHERE username = :username";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':pass' => password_hash($data['pass'], PASSWORD_BCRYPT), // Encriptar nueva contraseña
                ':username' => $username
            ]);

            return true;

        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    // Eliminar un usuario
    public static function deleteUser($pdo, $username) {
        try {
            $sql = "DELETE FROM Users WHERE username = :username";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([':username' => $username]);

            return true;

        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }
}

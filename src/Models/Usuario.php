<?php
namespace App\Models;

use PDO;

class Usuario
{
    private $db;
    private $table = 'usuarios';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Métodos del modelo aquí...
    public function crear(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (username, password, email, created_at) 
                VALUES 
                (:username, :password, :email, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':username' => $data['username'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':email'    => $data['email']
        ]);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET 
                username = :username, 
                email = :email 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id'       => $id,
            ':username' => $data['username'],
            ':email'    => $data['email']
        ]);
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }


    public function buscarPorCredenciales(string $username, string $password): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        
        return null;
    }

    public function existeUsuario(string $username, string $email): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->table} 
            WHERE username = :username OR email = :email
        ");
        
        $stmt->execute([
            ':username' => $username,
            ':email'    => $email
        ]);
        
        return $stmt->fetchColumn() > 0;
    }


    public function asignarRol(int $usuarioId, int $rolId): bool
    {
        $sql = "INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (:usuario_id, :rol_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':rol_id'     => $rolId
        ]);
    }

    public function obtenerRoles(int $usuarioId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.* 
            FROM roles r
            JOIN usuario_roles ur ON r.id = ur.rol_id
            WHERE ur.usuario_id = :usuario_id
        ");
        
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
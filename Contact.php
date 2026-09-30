<?php
class Contact {
    private $conn;
    private $table_name = "contacts";

    // Propiedades del Objeto
    public $id;
    public $name;
    public $phone;
    public $email;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LEER TODOS O BUSCAR
    public function read($search = "") {
        if (!empty($search)) {
            // Consulta de búsqueda con operador LIKE
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE name LIKE :search OR phone LIKE :search OR email LIKE :search 
                      ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
            $search_term = "%{$search}%";
            $stmt->bindParam(":search", $search_term);
        } else {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt;
    }

    // 2. CREAR CONTACTO
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, phone, email, category) 
                  VALUES (:name, :phone, :email, :category)";
        $stmt = $this->conn->prepare($query);

        // Sanitización de entradas
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->category = htmlspecialchars(strip_tags($this->category));

        // Vinculación de parámetros
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":category", $this->category);

        return $stmt->execute();
    }

    // 3. ELIMINAR CONTACTO
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
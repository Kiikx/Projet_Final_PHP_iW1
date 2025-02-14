<?php
class QueryBuilder
{
    private string $table;          // Nom de la table
    private array $fields = [];     // Colonnes sélectionnées ou à modifier
    private array $conditions = []; // Clauses WHERE
    private array $bindings = [];   // Valeurs des paramètres pour requêtes préparées

    private PDO $pdo;               // Instance PDO pour exécuter les requêtes

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    // SELECT
    public function select(array $fields = ['*']): self
    {
        $this->fields = $fields;
        return $this;
    }

    // WHERE
    public function where(string $field, string $operator, $value): self
    {
        $this->conditions[] = "$field $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    //OR WHERE
    public function orWhere(string $field, string $operator, $value): self
    {
        if (!empty($this->conditions)) {
            $this->conditions[] = "OR $field $operator ?";
        } else {
            $this->conditions[] = "$field $operator ?";
        }
        $this->bindings[] = $value;
        return $this;
    }

    // INSERT
    public function insert(array $data): bool
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->bindings = array_values($data);

        return $this->execute($sql);
    }

    // UPDATE
    public function update(array $data): bool
    {
        $updates = [];
        $bindings = [];
    
        foreach ($data as $field => $value) {
            $updates[] = "$field = ?";
            $bindings[] = $value;
        }
    
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates);
    
        if (!empty($this->conditions)) {
            $sql .= " WHERE (" . implode(' ', $this->conditions) . ")";
        }
    
        $this->bindings = array_merge($bindings, $this->bindings);
    
        return $this->execute($sql);
    }

    // DELETE
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        return $this->execute($sql);
    }

    // Générer une requête SELECT
    public function get(): array
    {
        $fields = implode(', ', $this->fields);
        $sql = "SELECT $fields FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        return $this->fetchAll($sql);
    }

    // Exécuter une requête avec des bindings
    private function execute(string $sql): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    // Récupérer les résultats (fetchAll)
    private function fetchAll(string $sql): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}



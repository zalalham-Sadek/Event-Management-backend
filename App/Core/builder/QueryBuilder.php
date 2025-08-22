<?php
namespace App\Core\builder;

class QueryBuilder {
    protected $table;
    protected $columns = "*";
    protected $conditions = [];
    protected $bindings = [];
    protected $joins = [];
    protected $limit;
    protected $order;

    public static function table($table) {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function select($columns = "*") {
        $this->columns = is_array($columns) ? implode(", ", $columns) : $columns;
        return $this;
    }

    public function where($column, $operator, $value) {
        $this->conditions[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function join($table, $first, $operator, $second) {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    public function orderBy($column, $direction = "ASC") {
        $this->order = "ORDER BY $column $direction";
        return $this;
    }

    public function limit($limit) {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    public function toSql() {
        $sql = "SELECT {$this->columns} FROM {$this->table} ";
        if ($this->joins) {
            $sql .= implode(" ", $this->joins) . " ";
        }
        if ($this->conditions) {
            $sql .= "WHERE " . implode(" AND ", $this->conditions) . " ";
        }
        if ($this->order) {
            $sql .= $this->order . " ";
        }
        if ($this->limit) {
            $sql .= $this->limit;
        }
        return trim($sql);
    }

    public function getBindings() {
        return $this->bindings;
    }
}

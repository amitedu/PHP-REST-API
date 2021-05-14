<?php


class Pages
{
    private $conn;
    private $table_name = 'pages';

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function read()
    {
        $query = "SELECT slug, title FROM " . $this->table_name . " ORDER BY orderId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        return $statement;
    }


    public function single($slug)
    {
        $query = "SELECT title, content FROM $this->table_name WHERE slug=:slug";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':slug', $slug);
        $statement->execute();
        return $statement;
    }

}
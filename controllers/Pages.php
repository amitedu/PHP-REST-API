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


    public function update($data)
    {
        $query = "UPDATE " . $this->table_name .
            " SET orderId = :orderId,
             title = :title, 
             content = :content
             WHERE id = :id";

        $statement = $this->conn->prepare($query);

        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':orderId', $data['orderId']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':content', $data['content']);

//        $statement->bindParam(':id', $data->id);
//        $statement->bindParam(':orderId', $data->orderId);
//        $statement->bindParam(':title', $data->title);
//        $statement->bindParam(':content', $data->content);

        if ($statement->execute()) {
            return true;
        }

        return false;
    }

}
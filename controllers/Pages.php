<?php

/**
 * @OA\Info(title="PHP-REST-API", version="1.0")
 */
class Pages
{
    private $conn;
    private $table_name = 'pages';


    public function __construct($db)
    {
        $this->conn = $db;
    }


    /**
     * @OA\Get(
     *     path="/v1/pages/read.php",
     *     tags= {"Pages"},
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="404", description="Not Found")
     * )
     */
    public function read()
    {
        $query = "SELECT slug, title FROM " . $this->table_name . " ORDER BY orderId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        return $statement;
    }


    /**
     * @OA\Post(
     *     path="/v1/pages/single.php",
     *     tags={"Pages"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(required={"slug"}, @OA\Property(property="slug", type="string"))
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found")
     * )
     */
    public function single($slug)
    {
        $query = "SELECT title, content FROM $this->table_name WHERE slug=:slug";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':slug', $slug);
        $statement->execute();
        return $statement;
    }


    /**
     * @OA\Put (
     *     path="/v1/pages/update.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 required={"id", "title", "content", "orderId"},
     *                 @OA\Property (property="id", type="integer", example=3),
     *                 @OA\Property (property="title", type="string", example="Contact Us"),
     *                 @OA\Property (property="content", type="string", example="<p>HTML Only</p>"),
     *                 @OA\Property (property="orderId", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found")
     * )
     */
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

        if ($statement->execute()) {
            return true;
        }

        return false;
    }


    /**
     * @OA\Post (
     *     path="/v1/pages/delete.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found")
     * )
     */
    public function delete($data)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $statement = $this->conn->prepare($query);
        $statement->bindParam(':id', $data['id']);

        return $statement->execute() && $statement->rowCount();
    }


    /**
     * @OA\Post (
     *     path="/v1/pages/create.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 required={"slug", "title", "content", "orderId"},
     *                 @OA\Property (property="slug", type="string", example="about"),
     *                 @OA\Property (property="title", type="string", example="About Us"),
     *                 @OA\Property (property="content", type="string", example="<p>HTML Only</p>"),
     *                 @OA\Property (property="orderId", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Success")
     * )
     */
    public function create(array $data)
    {
        $query = "INSERT INTO " . $this->table_name . " (orderId, slug, title, content) VALUES (:orderId, :slug, :title, :content)";
        $statement = $this->conn->prepare($query);

        $statement->bindParam(':orderId', $data['orderId']);
        $statement->bindParam(':slug', $data['slug']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':content', $data['content']);

        return (bool)$statement->execute();
    }
}
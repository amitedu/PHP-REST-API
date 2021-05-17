<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use \Firebase\JWT\JWT;

/**
 * @OA\Info(title="PHP-REST-API", version="1.0")
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use /auth to get the JWT token",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
class Pages {

    private $conn;
    private $table_name = 'pages';
    private $key        = 'privateKey';


    public function __construct($db)
    {
        $this->conn = $db;
    }


    /**
     * @OA\Get (
     *     path="/v1/pages/auth.php",
     *     tags={"Pages"},
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found")
     * )
     */
    public function auth(): array
    {
        $iat     = time();
        $exp     = $iat + 60 * 60;
        $payload = array(
            "iss" => "localhost", // Issuer
            "aud" => "livehost.test", // Audience
            "iat" => $iat, // time JWT was issued
            "exp" => $exp // time JWT expires
        );

        $jwt = JWT::encode($payload, $this->key, "HS512");

        return array(
            'token' => $jwt,
            'expires' => $exp
        );
    }


    /**
     * @OA\Get(
     *     path="/v1/pages/read.php",
     *     tags= {"Pages"},
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="404", description="Not Found"),
     *     security={ {"bearerAuth":{}} }
     * )
     */
    public function read()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) {
            $token = str_replace("Bearer ", "", $header['Authorization']);
            try {
                $token     = JWT::decode($token, $this->key, array("HS512"));
                $query     = "SELECT slug, title FROM " . $this->table_name . " ORDER BY orderId";
                $statement = $this->conn->prepare($query);
                $statement->execute();

                return $statement;
            } catch (\Exception $exception) {

                return false;
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/v1/pages/single.php",
     *     tags={"Pages"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(required={"slug"}, @OA\Property(property="slug", type="string", example="about"))
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found"),
     *     security={ {"bearerAuth":{}} }
     * )
     */
    public function single($slug)
    {
        $header = apache_request_headers();

        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];
            $token = str_replace("Bearer ", "", $token);

            try {
                $token     = JWT::decode($token, $this->key, array('HS512'));
                $query     = "SELECT title, content FROM $this->table_name WHERE slug=:slug";
                $statement = $this->conn->prepare($query);
                $statement->bindParam(':slug', $slug);
                $statement->execute();

                return $statement;
            } catch (Exception $e) {
                return false;
            }
        }
    }


    /**
     * @OA\Post (
     *     path="/v1/pages/create.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="application/json",
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
     *     @OA\Response (response="404", description="Success"),
     *     security={ {"bearerAuth":{}} }
     * )
     */
    public function create(array $data)
    {
        $header = apache_request_headers();

        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];
            $token = str_replace("Bearer ", "", $token);

            try {
                $token = JWT::decode($token, $this->key, array('HS512'));

                $query     = "INSERT INTO " . $this->table_name . " (orderId, slug, title, content) VALUES (:orderId, :slug, :title, :content)";
                $statement = $this->conn->prepare($query);
                $statement->bindParam(':orderId', $data['orderId']);
                $statement->bindParam(':slug', $data['slug']);
                $statement->bindParam(':title', $data['title']);
                $statement->bindParam(':content', $data['content']);

                if ($statement->execute()) {
                    return true;
                }

                return false;
            } catch (Exception $e) {
                echo "Token decode unsuccessful";
            }
        } else {
            return 'tokenUnsuccessful';
        }
    }


    /**
     * @OA\Put (
     *     path="/v1/pages/update.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="aplication/json",
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
     *     @OA\Response (response="404", description="Not Found"),
     *     security={ {"bearerAuth":{}} }
     * )
     */
    public function update($data)
    {
        $header = apache_request_headers();

        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];
            $token = str_replace("Bearer ", "", $token);

            try {
                $token = JWT::decode($token, $this->key, array('HS512'));
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

                return $statement->execute();
            } catch (Exception $e) {
                echo "Token decode unsuccessful";
            }
        } else {
            return 'tokenUnsuccessful';
        }
    }


    /**
     * @OA\Delete (
     *     path="/v1/pages/delete.php",
     *     tags={"Pages"},
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="application/json",
     *             @OA\Schema (
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response (response="200", description="Success"),
     *     @OA\Response (response="404", description="Not Found"),
     *     security={ {"bearerAuth":{}} }
     * )
     */
    public function delete($data)
    {
        $header = apache_request_headers();

        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];
            $token = str_replace("Bearer ", "", $token);

            try {
                $token     = JWT::decode($token, $this->key, array('HS512'));
                $query     = "DELETE FROM " . $this->table_name . " WHERE id = :id";
                $statement = $this->conn->prepare($query);

                $statement->bindParam(':id', $data['id']);

                return $statement->execute() && $statement->rowCount();
            } catch (Exception $e) {
                echo "Token decode unsuccessful";
            }
        } else {
            return 'tokenUnsuccessful';
        }
    }

}

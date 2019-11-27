<?php

namespace App\Models;

use PDO;

class Task extends \Core\Model
{
    public static $table_name = 'tasks';
    public static $sort_by = 'id';
    public static $sort_dir = 'ASC';
    private static $order_fields = [
        'name' => 'username',
        'email' => 'email',
        'status' => 'done',
    ];

    public static function find($page, $perPage = 10, $sortBy = '', $sortDir = '')
    {
        $limit = $perPage;
        $offset = ($page - 1) * $perPage;
        $sortBy = static::convertSortNameToModelFields($sortBy);
        $sortDir = static::convertSortDirectionToSqlDirection($sortDir);

        try {
            $table_name = static::$table_name;
            $db = static::getDB();
            $sql = "SELECT * FROM $table_name ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";
            $result = $db->prepare($sql);
            $result->bindParam(':limit', $limit, PDO::PARAM_INT);
            $result->bindParam(':offset', $offset, PDO::PARAM_INT);
            $result->execute();
            $results = $result->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function count()
    {
        try {
            $table_name = static::$table_name;
            $db = static::getDB();
            $sql = "SELECT COUNT(id) as count FROM $table_name";
            $result = $db->prepare($sql);
            $result->execute();
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function create($data)
    {
        try {
            $table_name = static::$table_name;
            $db = static::getDB();
            $sql = "INSERT INTO $table_name (username, email, content) VALUES (:username, :email, :content)";
            $result = $db->prepare($sql);
            $result->bindParam(':username', $username);
            $result->bindParam(':email', $email);
            $result->bindParam(':content', $content);

            $username = $data['username'];
            $email = $data['email'];
            $content = $data['text'];

            $result->execute();

        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function findOne($id)
    {
        try {
            $table_name = static::$table_name;
            $db = static::getDB();
            $sql = "SELECT * FROM $table_name WHERE id=:id";
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $result->execute();
            $results = $result->fetch(PDO::FETCH_ASSOC);

            return $results;

        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function update($data)
    {
        try {
            $table_name = static::$table_name;
            $db = static::getDB();
            $sql = "UPDATE $table_name SET username=:username, email=:email, content=:content, done=:done WHERE id=:id";
            $result = $db->prepare($sql);
            $result->bindParam(':username', $username);
            $result->bindParam(':email', $email);
            $result->bindParam(':content', $content);
            $result->bindParam(':done', $done, PDO::PARAM_INT);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $username = $data['username'];
            $email = $data['email'];
            $content = $data['text'];
            $done = !empty($data['done']) ? 1 : 0;
            $id = $data['id'];
            $results = $result->execute();

            if ($results && !empty($data['updated'])) {
                $sql = "UPDATE $table_name SET updated_at=:updated_at, updated=:updated WHERE id=:id";
                $result = $db->prepare($sql);
                $result->bindParam(':id', $id, PDO::PARAM_INT);
                $result->bindParam(':updated_at', $updatedAt);
                $result->bindParam(':updated', $updated);
                $id = $data['id'];
                $updatedAt = !empty($data['updated']) ? date('Y-m-d H:i:s') : date('0000-00-00 00:00:00');;
                $updated = !empty($data['updated']) ? 1 : 0;
                $result->execute();
            }
            return $results;

        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    private static function convertSortNameToModelFields($key)
    {
        if (array_key_exists($key, static::$order_fields)) {
            return static::$order_fields[$key];
        }

        return static::$sort_by;
    }

    private static function convertSortDirectionToSqlDirection($name)
    {
        if (!empty($name)) {
            return strtoupper($name);
        }

        return static::$sort_dir;
    }

}
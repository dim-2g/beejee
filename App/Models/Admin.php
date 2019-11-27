<?php

namespace App\Models;

use PDO;

class Admin extends \Core\Model
{
    private $table_name = 'users';

    public function getUserInfo($data)
    {
        try {
            $table_name = $this->table_name;
            $db = static::getDB();
            $sql = "SELECT * FROM $table_name WHERE login = :login AND active=1 AND admin=1";
            $result = $db->prepare($sql);
            $result->bindParam(':login', $login);
            $login = $data['login'];
            $result->execute();
            $results = $result->fetch(PDO::FETCH_ASSOC);

            return $results;

        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

}
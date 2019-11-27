<?php

namespace App\Controllers;

use Core\View;
use Core\Request;
use Core\Utils;
use App\Models\Admin;

class Admins extends \Core\Controller
{

    public function login()
    {
        $post = Request::post();
        $errors = [];

        if (Utils::isAdmin()) {
            Utils::redirect('/');
        }

        if (!empty($post['action']) && $post['action'] == 'login') {

            if ($this->isValidLogin($post)) {
                $_SESSION['isAdmin'] = true;
                Utils::redirect('/');
            } else {
                $errors[] = 'Неверные Имя пользователя или Пароль';
            }

        }

        View::renderTemplate('Admin/login.html', [
            'errors' => $errors,
        ]);


    }

    public function logout()
    {
        unset($_SESSION['isAdmin']);
        Utils::redirect('/admin/login');
    }

    private function isValidLogin($data)
    {
        if (!empty($data['login']) && !empty($data['password'])) {
            $admin = new Admin();
            $profile = $admin->getUserInfo($data);
            return $this->isPasswordCorrect($data['password'], $profile['password']);
        } else {
            return false;
        }
    }

    private function isPasswordCorrect($password, $hash)
    {
        return (md5($password) == $hash);
    }

}
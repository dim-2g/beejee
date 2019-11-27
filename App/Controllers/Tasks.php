<?php

namespace App\Controllers;

use Core\Utils;
use Core\View;
use Core\Request;
use Core\Paginator;
use Core\Sort;
use App\Models\Task;
use App\Config;

class Tasks extends \Core\Controller
{
    public function index()
    {
        $page = Request::getPage();
        $sortBy = Request::getValue(Config::VAR_SORT_BY);
        $sortDir = Request::getValue(Config::VAR_SORT_DIR);
        $total = Task::count();
        $posts = Task::find($page, Config::TASKS_PER_PAGE, $sortBy, $sortDir);
        $paginate = new Paginator($page, $total, Config::TASKS_PER_PAGE, '/tasks/index');
        $sort = new Sort(['name' => 'имени', 'email' => 'e-mail', 'status' => 'статусу'], '/tasks/index');

        View::renderTemplate('Tasks/list.html', [
            'posts' => $posts,
            'pagination' => $paginate->getHtml(),
            'sortpanel' => ($total > 0 ) ? $sort->getHtml() : null,
            'page' => $paginate->findCurrentPage(),
            'isAdmin' => Utils::isAdmin(),
        ]);
    }

    public function add()
    {
        $post = Request::post();
        $errors = [];
        $success = false;

        if (!empty($post['action']) && $post['action'] == 'add') {
            $validation = $this->isValidForm($post);
            if ($validation['success']) {
                Task::create($post);
                $success = true;
                $post = [];
            } else {
                $errors = $validation['errors'];
            }
        }

        View::renderTemplate('Tasks/add.html', [
            'isAdmin' => Utils::isAdmin(),
            'errors' => $errors,
            'data' => $post,
            'success' => $success,
        ]);
    }

    private function isValidForm($data)
    {
        $output = [
            'success' => true,
            'errors' => [],
        ];
        foreach (['username', 'email', 'text'] as $field) {
            $result = $this->isValidField($field, $data);
            if ($result !== true) {
                $output['success'] = false;
                $output['errors'] = array_merge($output['errors'], $result);    
            }
        }
        return $output;
    }

    private function isValidField($field, $data)
    {   
        switch ($field) {
            case 'username':
                if (!empty($data[$field])) {
                    return true;
                } else {
                    return ['Не заполнено Имя'];
                }           
            break;
            case 'text':
                if (!empty($data[$field])) {
                    return true;
                } else {
                    return ['Не заполнен Текст задачи'];
                }           
            break;
            case 'email':
                $errors = [];
                if (empty($data[$field])) {
                    $errors[] = 'Не заполнен Email';
                    return $errors;
                }
                if (!empty($data[$field])) {
                    if (!preg_match('#[^@]+@.+\..+$#siU', $data[$field])) {
                        $errors[] = 'Email не валиден';
                        return $errors;
                    }
                }
                return true;         
            break;
        }
    }

    public function edit()
    {
        if (!Utils::isAdmin()) {
            Utils::redirect('/admin/login');
        }

        $id = $this->getParam('id');
        if (empty($id)) Utils::redirect('/');
        $task = Task::findOne($id);
        if (!$task) Utils::redirect('/');

        $post = Request::post();
        if (!empty($post['action']) && $post['action']=='update') {
            if (!$task['updated'] && $task['content'] != $post['text']) {
                $post['updated'] = 1;
            }
            $result = Task::update($post);
            if ($result) {
                Utils::redirect('/');
            } else {
                echo "Save error";
            }
        }

        View::renderTemplate('Tasks/edit.html', [
            'task' => $task,
            'isAdmin' => Utils::isAdmin(),
        ]);
    }

}
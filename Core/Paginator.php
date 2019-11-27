<?php

namespace Core;

use Core\View;
use Core\Request;
use Core\Utils;

class Paginator
{
    private $current_page;
    private $total;
    private $limit;
    private $amount;
    private $sef_url;
    private $structure;

    public function __construct($page, $total, $limit, $sef_url)
    {
        $this->limit = $limit;
        $this->total = $total;
        $this->amount = $this->amount();
        $this->current_page = $page;
        $this->sef_url = $sef_url;
        $this->structure = $this->findStructure();
    }

    public function amount()
    {
        return ceil($this->total / $this->limit);
    }

    public function findCurrentPage()
    {
        return $this->current_page;
    }

    private function findStructure()
    {
        $data = [];
        for ($i = 0; $i < $this->amount; $i++) {
            $pageNum = $i + 1;
            $data[$i] = [
                'link' => $this->findPageLink($pageNum),
                'text' => $pageNum,
                'active' => ($pageNum == $this->findCurrentPage()),
            ];
        }
        return $data;
    }

    public function getHtml()
    {
        if ($this->amount > 1) {
            return View::getTemplate('Common/pagination.html', [
                'pagination' => $this->structure
            ]);
        } else {
            return null;
        }

    }

    private function findPageLink($page)
    {
        $get = Request::get();
        $get = array_merge($get, ['page'=>$page]);
        if ($page == 1) {
            unset($get['page']);
        }
        $queryString = http_build_query($get);

        if (!empty($queryString)) {
            return $this->sef_url . '?' . $queryString;
        } else {
            return $this->sef_url;
        }

    }

}
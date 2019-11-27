<?php

namespace Core;

use App\Config;

class Sort
{
    protected $structute = [];
    protected $sef_url;

    public function __construct($data, $sef_url)
    {
        $this->sef_url = $sef_url;
        $this->structute = $this->setStructure($data);
    }

    private function setStructure($data)
    {
        $mas = [];
        foreach ($data as $key => $item) {
            $mas[] = [
                'name' => $key,
                'title' => $item,
                'link' => $this->getPageLink($key),
                'dir' => $this->getLinkDirection($key),
            ];
        }
        return $mas;
    }

    public function getHtml()
    {
        return View::getTemplate('Common/sortpanel.html', [
            'data' => $this->structute
        ]);
    }

    private function getPageLink($sortBy)
    {
        $get = array_merge(Request::get(), [
            Config::VAR_SORT_BY => $sortBy,
            Config::VAR_SORT_DIR => $this->getLinkDirection($sortBy, 'asc'),
        ]);
        $get = array_diff($get, [Config::VAR_SORT_DIR => '']);
        $queryString = http_build_query($get);
        if (!empty($queryString)) {
            return $this->sef_url . '?' . $queryString;
        } else {
            return $this->sef_url;
        }
    }

    private function getLinkDirection($sortBy, $defaultDir = '')
    {
        $sortByQueryString = Request::getValue(Config::VAR_SORT_BY);
        $sortDirQueryString = Request::getValue(Config::VAR_SORT_DIR);
        $sortDir = $defaultDir;
        if ($sortBy == $sortByQueryString) {
            if ($sortDirQueryString == 'desc') {
                $sortDir = 'asc';
            } else {
                $sortDir = 'desc';
            }
        }
        return $sortDir;
    }

}
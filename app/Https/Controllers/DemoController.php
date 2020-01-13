<?php
/**
 * Demo控制器
 */

namespace App\Https\Controllers;

use Library\Https\Controller;

class DemoController extends Controller
{
    public function welcome($params)
    {
        return $this->response->json(['hello' => 'welcome']);
    }

    public function test($params)
    {
        return $this->response->json($params);
    }
}

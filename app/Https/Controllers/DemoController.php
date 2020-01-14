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

        // session_destro();
        // $_SESSION['sssss'] = 'ccc';
        return $this->response->json(['hello' => $_SESSION]);
    }

    public function test($params)
    {
        return $this->response->json($params);
    }
}

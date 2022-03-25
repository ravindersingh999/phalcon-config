<?php

use Phalcon\Mvc\Controller;
use Phalcon\Config;


class TestController extends Controller
{
    public function configAction()
    {
        // return '<h1>Hello World!</h1>';
        // $this->view->name = $this->config->get('app')->get('name');
        $this->view->name = $this->config->app->name;
        // $this->view->name = $this->config->app->version; 
    }
}
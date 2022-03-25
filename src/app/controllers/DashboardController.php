<?php

use Phalcon\Mvc\Controller;


class DashboardController extends Controller
{
    public function indexAction()
    {
        if(!$this->session->get("userE")) {
            header('location:login');
        }
        $this->view->time = $this->time;
    }
}
<?php

require_once __DIR__ . '/Controller.php';

class HomeController extends Controller 
{
    public function index(): void 
    {
        $clipboards = [];

        $this->render('home/index', [
            'clipboards' => $clipboards
        ], 'Начало');
    }
}
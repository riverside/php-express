<?php
namespace controllers;

class basic
{
    public function about($req, $res)
    {
        $res->render('about');
    }

    public function home($req, $res)
    {
        $res->render('home');
    }
}
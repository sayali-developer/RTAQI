<?php

namespace RTAQI\Controllers;

use League\Plates\Engine;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($view->render('userprofile'));
        return $response;
    }
}
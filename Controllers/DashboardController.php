<?php

namespace RTAQI\Controllers;

use League\Plates\Engine;
use RTAQI\Classes\User;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $user = new User();
        if (!$user->loggedIn()) {
            return new RedirectResponse(urlencode('/login/?message=You have to be logged in to access this page'));
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($view->render("dashboard"));
        return $response;
    }
}
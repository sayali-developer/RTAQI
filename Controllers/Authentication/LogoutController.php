<?php
namespace RTAQI\Controllers\Authentication;

use League\Plates\Engine;
use RTAQI\Classes\User;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogoutController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {

        $user = new User();
        $user->logout();

        $response = new RedirectResponse('/login/?message=Logout%20Successful');
        return $response->setStatusCode(302);

    }
}
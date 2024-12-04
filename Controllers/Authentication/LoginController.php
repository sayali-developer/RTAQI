<?php

namespace RTAQI\Controllers\Authentication;

use League\Plates\Engine;
use RTAQI\Classes\User;
use RTAQI\Exceptions\InvalidInputException;
use RTAQI\Exceptions\UserAuthenticationFailedException;
use RTAQI\Exceptions\UserNotActiveException;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'text/html; charset=utf-8');
        $user = new User();
        if ($request->getMethod() == 'GET') {
            if ($user->loggedIn()) {
                return $this->successRedirect();
            }
            $response->setContent($view->render("authentication/login"));
        } else if ($request->getMethod() == 'POST') {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            try {
                $user->logout();
                $user->loginUser($email, $password);
                if ($user->loggedIn()) {
                    return $this->successRedirect();
                }
            } catch (UserAuthenticationFailedException|UserNotActiveException|InvalidInputException $e) {
                $response->setContent($view->render("authentication/login", [
                    "message" => [
                        "type" => "danger",
                        "text" => $e->getMessage()
                    ]
                ]));

            }
        }
        return $response;
    }

    private function successRedirect(): Response
    {

        $response = new RedirectResponse($_GET["redirect"] ?? "/dashboard/");
        $response->setStatusCode(Response::HTTP_TEMPORARY_REDIRECT);
        return $response;
    }
}
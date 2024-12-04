<?php


namespace RTAQI\Controllers\Authentication;

use League\Plates\Engine;
use RTAQI\Classes\User;
use RTAQI\Exceptions\UserAuthenticationFailedException;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'text/html; charset=utf-8');

        if ($request->getMethod() == 'POST' && $request->request->has('email') && $request->request->has('name')) {
            $fullName = htmlspecialchars_decode(trim($request->request->get('name')));
            $email = strtolower(filter_var($request->request->get('email'), FILTER_SANITIZE_EMAIL));

            if ((strlen($fullName) < 3 || strlen($fullName) > 128) && (strlen($email) < 3)) {
                $response->setContent($view->render("authentication/register", [
                    "message" => [
                        "type" => "danger",
                        "text" => "Please check your name, email address and try again."
                    ]
                ]));
            } else {
                try {
                    $user = new User();

                    $user->createUser($email, $fullName);
                    $response->setContent($view->render("authentication/register", [
                        "message" => [
                            "type" => "success",
                            "text" => "Your account has been created. Please check your email to activate your account and set password."
                        ]
                    ]));

                } catch (UserAuthenticationFailedException $e) {
                    $response->setContent($view->render("authentication/register", [
                        "message" => [
                            "type" => "danger",
                            "text" => $email . " already exists. Please try to login using your password."
                        ]
                    ]));
                } catch (\Exception) {
                    $response->setContent($view->render("authentication/register", [
                        "message" => [
                            "type" => "danger",
                            "text" => "User has been successfully created, but failed to send email, please reset your password in some time."
                        ]
                    ]));
                }
            }
        }
        else {
            $response->setContent($view->render("authentication/register"));
        }
        return $response;
    }
}
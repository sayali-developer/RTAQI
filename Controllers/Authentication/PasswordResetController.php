<?php

namespace RTAQI\Controllers\Authentication;

use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;
use RTAQI\Classes\User;
use RTAQI\Exceptions\InvalidInputException;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html; charset=utf-8');

        $code = $request->get("code");
        $email = $request->get("email");
        $password = $request->get("password");
        $confirm_password = $request->get("confirm_password");

        $user = new User();

        $user->logout();

        if ($request->getMethod() == "GET") {
            if ($code == null) {
                $response->setContent($view->render("authentication/reset-password"));
            }
            else {
                try {

                    $resetDetails = $user->getResetCodeDetails($request->get('code'));

                    $response->setContent($view->render("authentication/reset-password", [
                        "email" => $resetDetails['email'],
                        "code" => $code
                    ]));
                } catch (InvalidInputException $e) {
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ]
                    ]));
                }
            }

            return $response;
        } else if ($request->getMethod() == "POST") {
            if ($code == null && $email != null) {
                try {
                    $user = new User();

                    $user->getUser($email);
                    $user->sendPasswordResetEmail();
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "success",
                            "text" => "Password reset link has been sent on ".$email
                        ]
                    ]));
                } catch (InvalidInputException $e) {
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ]
                    ]));
                } catch (\Exception $e) {
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "warning",
                            "text" => "Failed to send email, please try again."
                        ]
                    ]));
                } finally {
                    return $response;
                }
            }
            if ($code != null && $password != null && $confirm_password != null) {
                if (($password != $confirm_password) || strlen($password) < 8) {
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "warning",
                            "text" => "Passwords do not match or length of password is less than 8, please use the link again to reset password."
                        ]
                    ]));
                    return $response;
                }
                try {
                    $user = new User();
                    $user->resetPassword($code, $password);
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "success",
                            "text" => "Password reset successfully. Please login using new password."
                        ]
                    ]));
                } catch (InvalidInputException $e) {
                    $response->setContent($view->render("authentication/reset-password", [
                        "message" => [
                            "type" => "danger",
                            "text" => $e->getMessage()
                        ]
                    ]));
                } finally {
                    return $response;
                }
            }
        }
        return $response;
    }
}
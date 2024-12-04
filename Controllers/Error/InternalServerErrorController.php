<?php


namespace RTAQI\Controllers\Error;


use League\Plates\Engine;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalServerErrorController implements ControllerInterface
{

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        // TODO: Implement handler() method.
        $response = new Response();
        $response->setContent($view->render("errors/500", [
            "error" => $params
        ]));
        $response->setStatusCode(500);
        return $response;
    }
}
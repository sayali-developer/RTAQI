<?php


namespace RTAQI\Controllers\Error;


use League\Plates\Engine;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageNotFoundController implements ControllerInterface
{

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();
        $response->setContent($view->render("errors/404", [
            "url" => $request->getRequestUri()
        ]));
        $response->setStatusCode(404);
        return $response;
    }
}
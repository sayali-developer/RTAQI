<?php
namespace RTAQI\Controllers\API;

use League\Plates\Engine;
use RTAQI\Classes\AQIService;
use RTAQI\Framework\Interfaces\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshDataController implements ControllerInterface {

    public function handler(Request $request, Engine $view, array $params = []): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $start_time = microtime(true);
        $apiService = new AQIService();
        $apiService->processRawData();
        $end_time = microtime(true);
        $time_took = $end_time - $start_time;

        $response->setContent(json_encode([
            "status" => "success",
            "message" => [
                "type" => "success",
                "text" =>"Time took - " . $time_took . " seconds"
            ],
            "data" => [
                "start_time" => $start_time,
                "end_time" => $end_time,
                "memory_usage" => memory_get_usage()
            ]
        ]));
        return $response;
    }
}
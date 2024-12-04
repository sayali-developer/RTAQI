<?php


namespace RTAQI\Framework\Interfaces;


use League\Plates\Engine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    public function handler(Request $request, Engine $view, array $params = []): Response;
}
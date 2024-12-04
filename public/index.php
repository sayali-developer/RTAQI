<?php
namespace RTAQI;

// Autoload Composer dependencies
require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config.php';

use Exception;
use League\Plates\Engine;
use RTAQI\Controllers\API\GetController;
use RTAQI\Controllers\API\RefreshDataController;
use RTAQI\Controllers\Authentication\LoginController;
use RTAQI\Controllers\Authentication\PasswordResetController;
use RTAQI\Controllers\Authentication\RegistrationController;
use RTAQI\Controllers\Authentication\LogoutController;

use RTAQI\Controllers\DashboardController;
use RTAQI\Controllers\Error\InternalServerErrorController;
use RTAQI\Controllers\HomepageController;
use RTAQI\Controllers\UserProfileController;
use RTAQI\Framework\Classes\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Set Timezone to India
date_default_timezone_set("Asia/Calcutta");


// Initialize Plates Template Engine
$viewEngine = new Engine(__DIR__ . '/../Views');

// Start the Session
session_start();

// Handle the incoming HTTP request
$request = Request::createFromGlobals();

$router = new Router($request, $viewEngine);

try {
    $router->addRoute(HomepageController::class, ["/", ""]);

    // Authentication Router
    $router->addRoute(LoginController::class, ["/login", "/login/"]);
    $router->addRoute(RegistrationController::class, ["/register", "/register/"]);
    $router->addRoute(PasswordResetController::class,["/reset-password", "/reset-password/"]);
    $router->addRoute(LogoutController::class, ["/logout", "/logout/"]);

    //API Routes
    $router->addRoute(RefreshDataController::class, ["/api/refresh-data", "/api/refresh-data/"]);
    $router->addRoute(GetController::class, ["/api/get", "/api/get/"]);

    //User Login
    $router->addRoute(DashboardController::class, ["/dashboard", "/dashboard/"]);
    $router->addRoute(UserProfileController::class, ["/profile", "/profile/"]);

    $response = $router->respond();

} catch (Exception $e) {

    $internalServerErrorController = new InternalServerErrorController();

    $response = $internalServerErrorController->handler($request, $viewEngine, [
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);

}

$response->send();

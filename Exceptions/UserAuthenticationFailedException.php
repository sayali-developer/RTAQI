<?php

namespace RTAQI\Exceptions;

use Exception;
use Throwable;

class UserAuthenticationFailedException extends Exception implements Throwable
{


    /**
     * UserAuthenticationFailedException constructor.
     * @param string $message
     */
    public function __construct(string $message = "")
    {
        if ($message == "")
            parent::__construct("Failed to login, please enter valid email address or password.");
        else
            parent::__construct($message);

    }

}
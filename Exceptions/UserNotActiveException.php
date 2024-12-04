<?php

namespace RTAQI\Exceptions;

use Exception;
use RTAQI\Classes\User;
use Throwable;

class UserNotActiveException extends Exception implements Throwable
{


    /**
     * UserNotActiveException constructor.
     */
    public function __construct(User $user)
    {
        parent::__construct("Unable to login as account status of user is set to inactive. Please use password reset link to verify your account so it can be activated.");
        $user->logout();
    }

}

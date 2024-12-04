<?php


namespace RTAQI\Exceptions;


class InvalidInputException extends \Exception implements \Throwable
{
    private $field;

    public function __construct($message, $field = null)
    {
        parent::__construct($message);
        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }
}
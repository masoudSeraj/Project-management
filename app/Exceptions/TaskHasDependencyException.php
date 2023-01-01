<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskHasDependencyException extends Exception
{
    public function report()
    {

    }

    public function render()
    {
    }
}

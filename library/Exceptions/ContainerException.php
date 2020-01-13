<?php


namespace Library\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends SaiException implements ContainerExceptionInterface
{
    protected $code = 500;
}

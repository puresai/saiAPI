<?php


namespace Library\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends SaiException implements NotFoundExceptionInterface
{
    protected $code = 500;
}

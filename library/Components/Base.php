<?php

namespace Library\Components;

class Base implements \ArrayAccess
{
    private $container;

    public function __get($name)
    {
        if (method_exists($this, $method = 'get'.ucfirst($name))) {
            return $this->$method($name);
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (method_exists($this, $method = 'set'.ucfirst($name))) {
            return $this->$method($name, $value);
        }
    }

    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

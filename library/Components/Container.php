<?php

namespace Library\Components;

use Library\Exceptions\ContainerException;
use Library\Exceptions\ContainerNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    // 用于保存依赖的定义，以对象名称为键
    private $definitions = [];

    // 用于缓存ReflectionClass对象，以对象名称为键
    private $reflections = [];

    // 用于缓存依赖信息，以对象名称为键
    private $dependencies = [];

    public function has($class)
    {
        return isset($this->definitions[$class]);
    }

    public function get($class)
    {
        // 加入未作set操作，我们依旧可以构建
        if (!isset($this->definitions[$class])) {
            return $this->build($class);
        }

        $definition = $this->definitions[$class];
        if (is_array($definition)) {
            $concrete = $definition['class'];
            unset($definition['class']);

            if ($concrete === $class) {
                $object = $this->build($class, $definition);
            } else {
                $object = $this->get($concrete);
            }
        } elseif (is_object($definition)) {
            return $this->_singletons[$class] = $definition;
        } else {
            throw new ContainerNotFoundException('不能识别的对象类型: ' . gettype($definition));
        }
        
        return $object;

    }

    public function set($class, $definition = [])
    {
        $this->definitions[$class] = $this->normalizeDefinition($class, $definition);
        return $this;
    }

    protected function normalizeDefinition($class, $definition)
    {
        // $definition 是空的转换成 ['class' => $class] 形式
        if (empty($definition)) {
            return ['class' => $class];

            // $definition 是字符串，转换成 ['class' => $definition] 形式
        } elseif (is_string($definition)) {
            return ['class' => $definition];

            // $definition 是对象，则直接将其作为依赖的定义
        } elseif (is_object($definition)) {
            return $definition;

            // $definition 是数组则确保该数组定义了 class 元素
        } elseif (is_array($definition)) {
            if (!isset($definition['class'])) {
                $definition['class'] = $class;
            }
            return $definition;
            // 这也不是，那也不是，那就抛出异常算了
        } else {
            throw new ContainerException(
                "不支持的类型： \"$class\": " . gettype($definition));
        }
    }

    public function build($class, $params = [])
    {
        try {
            // 通过反射api获取对象
            $reflector = $this->getReflectionClass($class);
            // p($reflector);
            
            // 获取依赖关系数组
            $dependencies = $this->getDependencies($class, $reflector);

            // 创建一个类的新实例,给出的参数将传递到类的构造函数.
            $reflector =  $reflector->newInstanceArgs($dependencies);
            
            return $reflector;
        } catch (\Throwable $t) {
            throw new ContainerException('反射出错');
        }
    }

    public function getReflectionClass($class)
    {
        if (isset($this->reflections[$class])) {
            return $this->reflections[$class];
        }

        $reflector = new ReflectionClass($class);
        if (!$reflector->isInstantiable()) {
            throw new ContainerException("不能实例化".$class);
        }

        return $this->reflections[$class] = $reflector;
    }

    public function getDependencies($class, $reflector)
    {
        // 判断是否有缓存依赖关系
        if (isset($this->dependencies[$class])) {
            return $this->dependencies[$class];
        }
        $constructor = $reflector->getConstructor();

        // 如果没有构造函数， 直接实例化并返回
        if (is_null($constructor)) {
            return $this->dependencies[$class] = [];
        }

        $parameters = $constructor->getParameters();

        $dependencies = [];
        foreach ($parameters as $className) {
            $dependency = $className->getClass();

            if (is_null($dependency)) {
                $dependencies[] = $this->resolveNoneClass($className);
            } else {
                // 先取出容器中绑定的类 否则自动绑定
                $dependencies[] = $this->get($dependency->getName());
            }
        }
        
        $this->dependencies[$class] = $dependencies;

        return $dependencies;
    }

    public function resolveNoneClass($class)
    {
        // 有默认值则返回默认值
        if ($class->isDefaultValueAvailable()) {
            return $class->getDefaultValue();
        }
        throw new ContainerException('不能解析参数');
    }
}
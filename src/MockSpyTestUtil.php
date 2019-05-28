<?php

namespace TestMock;

/**
 * Essa classe mixa a MockTestUtil e é utilizada para criar um objeto de spy dinâmico.
 * Recebe o objeto mockado como parâmetro do construtor.
 * 
 * 
 * @author Anderson Nogueira
 */
class MockSpyTestUtil {

    use MockTestUtil;

    protected $object;
    protected $throwErrorMethodNotExists;

    public function __construct($object = null, $throwErrorMethodNotExists = true) {
        $this->object = isset($object) ? $object: new class {};
        $this->throwErrorMethodNotExists = $throwErrorMethodNotExists;
    }

    public function __call($method, $args) {
        
        $reflection = new \ReflectionObject($this->object);
        
        if ($this->throwErrorMethodNotExists && !method_exists($this->object, $method)) {
            throw new \Exception("Method `$method` not found!");
        }
        
        $this->addMockCall($method, $args);
        if (!array_key_exists($method, $this->_mockReturns) && isset($this->object)) {
            /* @var $reflectionMethod \ReflectionMethod */
            $reflectionMethod = $reflection->getMethod($method);
            $reflectionMethod->setAccessible(true);
            if (isset($args)) {
                return $reflectionMethod->invokeArgs($this->object, $args);
            } else {
                return $reflectionMethod->invoke($this->object);
            }
        } else {
            return $this->_mockReturns[$method];
        }
    }

    function setObject($object) {
        $this->object = $object;
        return $this;
    }

    function setThrowErrorMethodNotExists(bool $throwErrorMethodNotExists) {
        $this->throwErrorMethodNotExists = $throwErrorMethodNotExists;
        return $this;
    }

}
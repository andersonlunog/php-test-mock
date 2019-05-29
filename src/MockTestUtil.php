<?php

namespace TestMock;

/**
 * Essa trait é composta de um conjunto de funcionalidades úteis para realização de testes por meio de mocks. 
 * A classe de mock deve estender a classe original, dar o `use MockTestUtil` e sobrescrever os métodos a serem mockados.
 * 
 * @author Anderson Nogueira
 */
trait MockTestUtil {

    protected $_mockReturns = [];
    protected $_callsSpy = [];

    /**
     * Deve ser chamado dentro do método sobrescrito a ser mockado.
     * A chamada deve ser feita da seguinte forma: return $this->mockMethod(__FUNCTION__, func_get_args());
     * 
     * @param type $method
     * @param type $args
     * @return type
     */
    protected function mockMethod($method, $args) {
        $ret;
        if (!array_key_exists($method, $this->_mockReturns)) {
            $methodOrig = array(get_parent_class($this), $method);
            $ret = call_user_func_array($methodOrig, $args);            
        } else {
            $ret = $this->_mockReturns[$method];
        }
        $this->addMockCall($method, $args, $ret);
        return $ret;
    }

    /**
     * Método que guarda as chamadas ao método, seus parâmetros e retorno.
     * 
     * @param type $method
     * @param type $args
     * @param type $return
     */
    protected function addMockCall($method, $args, $return) {
        if (!isset($this->_callsSpy[$method])) {
            $this->_callsSpy[$method] = [];
        }
        $call = [
            "return" => $return,
            "arguments" => $args
        ];
        array_push($this->_callsSpy[$method], $call);
    }

    /**
     * Substitui o retorno de um método da classe.
     * 
     * @param string $method
     * @param type $return
     * @throws Exception
     */
    public function setMockReturn(string $method, $return) {
        // if (!in_array($method, get_class_methods($this))) {
        //     throw new Exception("Method $method not found.");
        // }
        $this->_mockReturns[$method] = $return;
    }

    /**
     * Cancela a substituição do retorno de um método da classe, voltando ao seu comportamento original.
     * 
     * @param string $method
     */
    public function resetMethod(string $method) {
        unset($this->_mockReturns[$method]);
        unset($this->_callsSpy[$method]);
    }

    /**
     * Cancela a substituição do retorno de todos os métodos da classe, voltando aos seus comportamentos originais.
     */
    public function resetAll() {
        $this->_mockReturns = [];
        $this->resetSpyAll();
    }

    /**
     * Reseta o spy de todos os métodos.
     */
    public function resetSpyAll() {
        $this->_callsSpy = [];
    }

    /**
     * Inicia um assert das chamadas de um método utilizando um design fluente. Ex.:
     *  $mock->assertCalls("metodoXPTO")->notCalled();
     * 
     * 
     * @param string $method
     * @return \TestMock\MockTestUtilCalls
     */
    public function assertCalls(string $method) {
        if (!isset($this->_callsSpy[$method])) {
            $this->_callsSpy[$method] = [];
        }
        return new MockTestUtilCalls($method, $this->_callsSpy[$method]);
    }

    /**
     * Cria uma classe mock que mixa MockTestUtil por reflexão.
     */
    public static function createMock($className) {
        $reflection = new \ReflectionClass($className);
        $classMockName = '___' . $reflection->getShortName() . 'Mock';
        $classStr = 'class ' . $classMockName . ' extends \\' . $className . " {\n";
        $classStr .= "    use \\" . MockTestUtil::class . "; \n";
        foreach ($reflection->getMethods() as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()) {
                $methStr = MockTestUtil::getStringOfMethod($reflectionMethod);
                $classStr .= preg_replace('/{.*}/s', '{ return $this->mockMethod(__FUNCTION__, func_get_args()); }', $methStr) . "\n";
            }
        }
        $classStr .= "}";
        eval($classStr);
        return $classMockName;
    }

    private static function getStringOfMethod(\ReflectionMethod $reflectionMethod) {
        $filename = $reflectionMethod->getFileName();
        $start_line = $reflectionMethod->getStartLine() - 1;
        $end_line = $reflectionMethod->getEndLine();
        $length = $end_line - $start_line;
        $source = file($filename);
        $body = implode("", array_slice($source, $start_line, $length));
        return $body;
    }

}

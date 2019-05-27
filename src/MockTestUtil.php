<?php

namespace AuthorizationTest\TestUtil;

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
        $this->addMockCall($method, $args);
        if (!array_key_exists($method, $this->_mockReturns)) {
            $method = array(get_parent_class($this), $method);
            return call_user_func_array($method, $args);            
        }
        return $this->_mockReturns[$method];
    }

    /**
     * Método que guarda as chamadas ao método e seus parâmetros.
     * 
     * @param type $method
     * @param type $args
     */
    protected function addMockCall($method, $args) {
        if (!isset($this->_callsSpy[$method])) {
            $this->_callsSpy[$method] = [];
        }
        array_push($this->_callsSpy[$method], $args);
    }

    /**
     * Substitui o retorno de um método da classe.
     * 
     * @param \AuthorizationTest\TestUtil\String $method
     * @param type $return
     * @throws Exception
     */
    public function setMockReturn(String $method, $return) {
        // if (!in_array($method, get_class_methods($this))) {
        //     throw new Exception("Method $method not found.");
        // }
        $this->_mockReturns[$method] = $return;
    }

    /**
     * Cancela a substituição do retorno de um método da classe, voltando ao seu comportamento original.
     * 
     * @param \AuthorizationTest\TestUtil\String $method
     */
    public function resetMethod(String $method) {
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
     * @param \AuthorizationTest\TestUtil\String $method
     * @return \AuthorizationTest\TestUtil\MockTestUtilCalls
     */
    public function assertCalls(String $method) {
        if (!isset($this->_callsSpy[$method])) {
            $this->_callsSpy[$method] = [];
        }
        return new MockTestUtilCalls($method, $this->_callsSpy[$method]);
    }

}

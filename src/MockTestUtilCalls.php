<?php

namespace AuthorizationTest\TestUtil;

/**
 * Classe responsável por validar as chamadas de um método.
 * 
 * @author Anderson Nogueira
 */
class MockTestUtilCalls extends \PHPUnit\Framework\TestCase {

    protected $_calls;
    protected $_method;

    public function __construct($method, $calls) {
        $this->_calls = $calls;
        $this->_method = $method;
    }
    
    /**
     * Verifica a quantidade de chamadas ao método.
     * 
     * @param \AuthorizationTest\TestUtil\Int $count
     * @return $this
     */
    public function withCount(Int $count) {
        $this->assertCount($count, $this->_calls, "Method $this->_method calls count.");
        return $this;
    }

    /**
     * Obtém os argumentos da chamada número x, utilizando um design fluente. Ex.:
     *  ...->getCall(0)->withoutArgs();
     * 
     * @param \AuthorizationTest\TestUtil\Int $callIndex
     * @return \AuthorizationTest\TestUtil\MockTestUtilCallArgs
     */
    public function getCall(Int $callIndex) {
        return new MockTestUtilCallArgs($this->_method, $callIndex, $this->_calls[$callIndex]);
    }

    /**
     * Verifica se o método foi chamado apenas uma vez e retorna os argumentos da chamada.
     * 
     * @return \AuthorizationTest\TestUtil\MockTestUtilCallArgs
     */
    public function calledOnce() {
        return $this->withCount(1)->getCall(0);
    }

    /**
     * Verifica se o método não foi chamado.
     * 
     * @return \AuthorizationTest\TestUtil\MockTestUtilCallArgs
     */
    public function notCalled() {
        return $this->withCount(0);
    }
    
    /**
     * Obtém todas as chamadas ao método.
     * 
     * @return type
     */
    public function getCalls() {
        return $this->_calls;
    }
    
    /**
     * Obtém a quantidade de chamdadas ao método.
     * 
     * @return type
     */
    public function callCount() {
        return sizeof($this->_calls);
    }

}
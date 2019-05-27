<?php

namespace AuthorizationTest\TestUtil;

/**
 * Classe responsável por validar os argumentos de uma chamada de um método.
 * 
 * @author Anderson Nogueira
 */
class MockTestUtilCallArgs extends \PHPUnit\Framework\TestCase {

    protected $_call;
    protected $_method;
    protected $_callIndex;
    protected $_prefix;


    public function __construct($method, $callIndex, $call) {
        $this->_method = $method;
        $this->_callIndex = $callIndex;
        $this->_call = $call;
        $this->_prefix = "Call[$callIndex] of method `$method`";
    }

    /**
     * Verifica se o método foi chamado sem nenhum argumento.
     * 
     * @return $this
     */
    public function withoutArgs() {
        $this->assertEmpty($this->_call, "$this->_prefix is not empty.");
        return $this;
    }
    
    /**
     * Verifica se o método foi chamado com os argumentos informados.
     * 
     * @param type $args
     * @return $this
     */
    public function withArgs($args) {
        $this->withArgsCount(sizeof($args));
        foreach ($args as $i => $arg) {
            $this->assertEquals($arg, $this->_call[$i], "$this->_prefix argument `$i`.");
        }
        return $this;
    }
    
    /**
     * Verifica se o método foi chamado com a quantidade de argumentos informada.
     * 
     * @param int $count
     * @return $this
     */
    public function withArgsCount(int $count) {
        $this->assertCount($count, $this->_call, "$this->_prefix args count.");
        return $this;
    }

    /**
     * Verifica se o método foi chamado com o argumento na posição informados.
     * 
     * @param \AuthorizationTest\TestUtil\Int $argIndex
     * @param type $arg
     * @return $this
     */
    public function withArg(Int $argIndex, $arg) {
        $this->assertEquals($arg, $this->getArg($argIndex), "$this->_prefix argument `$argIndex`.");
        return $this;
    }
    
    /**
     * Obtém o argumento pelo índice.
     * 
     * @param \AuthorizationTest\TestUtil\Int $index
     * @return type
     */
    public function getArg(Int $index) {
        return $this->_call[$index];
    }
    
    /**
     * Obtém todos os argumentos.
     * 
     * @return type
     */
    public function getArgs() {
        return $this->_call;
    }

}

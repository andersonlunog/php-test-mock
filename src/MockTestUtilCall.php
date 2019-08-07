<?php

namespace TestMock;

/**
 * Classe responsável por validar os argumentos e retorno de uma chamada de um método.
 * 
 * @author Anderson Nogueira
 */
class MockTestUtilCall extends \PHPUnit\Framework\TestCase {

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
        $this->assertEmpty($this->_call["arguments"], "$this->_prefix is not empty.");
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
            $this->assertEquals($arg, $this->_call["arguments"][$i], "$this->_prefix argument `$i`.");
        }
        return $this;
    }
    
    /**
     * Verifica se o método foi chamado com a quantidade de argumentos informada.
     * 
     * @param int $count
     * @return $this
     */
    public function withArgsCount($count) {
        $this->assertCount($count, $this->getArgs(), "$this->_prefix args count.");
        return $this;
    }

    /**
     * Verifica se o método foi chamado com o argumento na posição informados.
     * 
     * @param int $argIndex
     * @param type $arg
     * @return $this
     */
    public function withArg($argIndex, $arg) {
        $this->assertEquals($arg, $this->getArg($argIndex), "$this->_prefix argument `$argIndex`.");
        return $this;
    }
    
    /**
     * Obtém o argumento pelo índice.
     * 
     * @param int $index
     * @return type
     */
    public function getArg($index) {
        return $this->getArgs()[$index];
    }
    
    /**
     * Obtém o retorno do método invocado
     */
    public function getReturn() {
        return $this->_call["return"];
    }

    /**
     * Verifica o retorno do método chamado
     */
    public function withReturn($return) {
        $this->assertEquals($return, $this->getReturn(), "$this->_prefix return not correspondent.");
        return $this;
    }

    /**
     * Verifica se o retorno do método chamado é nulo
     */
    public function withoutReturn() {
        $this->assertEmpty($this->getReturn(), "$this->_prefix return not empty.");
        return $this;
    }
    
    /**
     * Obtém todos os argumentos.
     * 
     * @return type
     */
    public function getArgs() {
        return $this->_call["arguments"];
    }

}

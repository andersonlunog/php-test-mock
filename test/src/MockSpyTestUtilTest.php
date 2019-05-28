<?php

namespace TestMock;

class Foo1 {
    
    private $_bar;
    private $_baz;
    
    public function __construct(string $baz, $bar) {
        $this->_baz = $baz;
        $this->_bar = $bar;
    }

    protected function baz() {
        return $this->_baz;
    }

    public function bar() {
        return $this->_bar . $this->baz();
    }

    public function qux(string $param1, int $param2) {
        return "QUX: $param1 AND $param2";
    }
}

class MockSpyTestUtilTest extends \PHPUnit\Framework\TestCase {

    public function testTestMockSpy() {
        $fooSpy = new MockSpyTestUtil(new Foo1('BAZ!', 1));
        
        $this->assertEquals('BAZ!', $fooSpy->baz());
        $this->assertEquals('QUX: xpto AND 232', $fooSpy->qux("xpto", 232));
        $fooSpy->assertCalls('baz')->calledOnce()->withoutArgs();
        $fooSpy->assertCalls('qux')->calledOnce()->withArgs(["xpto", 232]);
                
        $fooSpy->setMockReturn('baz', 'MODIFIED!');
        $fooSpy->setMockReturn('qux', 'QUX!');
        $this->assertEquals('MODIFIED!', $fooSpy->baz());
        $this->assertEquals('QUX!', $fooSpy->qux("xpto", 232));
        $fooSpy->assertCalls('baz')->getCall(1)->withoutArgs();
        $fooSpy->assertCalls('qux')->getCall(1)->withArgs(["xpto", 232]);
    }

    public function testTestMockSpyErrorMethodNotExist() {
        $fooSpy = new MockSpyTestUtil();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Method `notExist` not found!");
        
        $fooSpy->notExist();
    }
}
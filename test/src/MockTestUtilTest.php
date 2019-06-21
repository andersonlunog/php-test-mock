<?php

namespace TestMockTest;

use TestMock\MockTestUtil;

class Foo {
    protected function baz() {
        return 'BAZ!';
    }

    public function bar() {
        return 'BAR.' . $this->baz();
    }

    public function qux($param1, $param2) {
        return "QUX: $param1 AND $param2";
    }
}

class Foo1 {
    
    private $_bar;
    private $_baz;
    
    public function __construct(string $baz, $bar) {
        $this->_baz = $baz;
        $this->_bar = $bar;
    }

    public static function myStatic() {
        return 'Static called';
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
    
    public function withoutReturn() {
        $this->bar();
    }
}

class MockTestUtilTest extends \PHPUnit\Framework\TestCase {

    public function testTestMockHerancaDireta() {
        $fooMock = new class() extends Foo {
            use \TestMock\MockTestUtil;

            protected function baz() {
                return $this->mockMethod(__FUNCTION__, func_get_args());
            }            
            public function bar() {
                return $this->mockMethod(__FUNCTION__, func_get_args());
            }
            public function qux($param1, $param2) {
                return $this->mockMethod(__FUNCTION__, func_get_args());
            }
        };

        $this->assertEquals('BAR.BAZ!', $fooMock->bar());
        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('bar')->calledOnce()->withoutArgs()->withReturn('BAR.BAZ!');
        $fooMock->assertCalls('baz')->calledOnce()->withoutArgs()->withReturn('BAZ!');
        $fooMock->assertCalls('qux')->calledOnce()->withArgs(['p1', 'p2'])->withReturn('QUX: p1 AND p2');

        $fooMock->setMockReturn('baz', 'MODIFIED!');
        $this->assertEquals('BAR.MODIFIED!', $fooMock->bar());
        $this->assertEquals(2, $fooMock->assertCalls('bar')->callCount());
        $fooMock->assertCalls('baz')->getCall(1)->withoutArgs()->withReturn('MODIFIED!');

        $fooMock->resetAll();
        $this->assertEquals('BAR.BAZ!', $fooMock->bar());
        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('bar')->calledOnce()->withoutArgs()->withReturn('BAR.BAZ!');
        $fooMock->assertCalls('baz')->calledOnce()->withoutArgs()->withReturn('BAZ!');
        $fooMock->assertCalls('qux')->calledOnce()->withArgs(['p1', 'p2'])->withReturn('QUX: p1 AND p2');
    }

    public function testTestMockReflexao() {
        $mockName = MockTestUtil::createMock(Foo::class);
        $fooMock = new $mockName;

        $this->assertEquals('BAR.BAZ!', $fooMock->bar());
        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('bar')->calledOnce()->withoutArgs()->withReturn('BAR.BAZ!');
        $fooMock->assertCalls('baz')->calledOnce()->withoutArgs()->withReturn('BAZ!');
        $fooMock->assertCalls('qux')->calledOnce()->withArgs(['p1', 'p2'])->withReturn('QUX: p1 AND p2');

        $fooMock->setMockReturn('baz', 'MODIFIED!');
        $this->assertEquals('BAR.MODIFIED!', $fooMock->bar());
        $this->assertEquals(2, $fooMock->assertCalls('bar')->callCount());
        $fooMock->assertCalls('baz')->getCall(1)->withoutArgs()->withReturn('MODIFIED!');

        $this->assertEquals('BAR.MODIFIED!', $fooMock->bar());
        $this->assertEquals(3, $fooMock->assertCalls('bar')->callCount());
        
        $fooMock->resetSpyAll();
        $this->assertEquals(0, $fooMock->assertCalls('bar')->callCount());
    }

    public function testTestMockReflexaoParametroConstrutor() {
        $classMockName = MockTestUtil::createMock(Foo1::class);
        $fooSpy = new $classMockName('BAZ!', 123);
        
        $this->assertEquals('QUX: xpto AND 232', $fooSpy->qux("xpto", 232));
        $this->assertEquals('123BAZ!', $fooSpy->bar());
        $fooSpy->assertCalls('baz')->calledOnce()->withoutArgs();
        $fooSpy->assertCalls('qux')->calledOnce()->withArgs(["xpto", 232]);
        $fooSpy->assertCalls('bar')->calledOnce()->withoutArgs();
                
        $fooSpy->setMockReturn('baz', 'MODIFIED!');
        $fooSpy->setMockReturn('qux', 'QUX!');
        $this->assertEquals('QUX!', $fooSpy->qux("xpto", 232));
        $this->assertEquals('123MODIFIED!', $fooSpy->bar());
        $fooSpy->assertCalls('baz')->getCall(1)->withoutArgs();
        $fooSpy->assertCalls('qux')->getCall(1)->withArgs(["xpto", 232]);

        $fooSpy->withoutReturn();
        $fooSpy->assertCalls('withoutReturn')->calledOnce()->withoutReturn();

        $fooSpy->resetAll();
        $this->assertEquals('QUX: xpto AND 232', $fooSpy->qux("xpto", 232));
        $this->assertEquals('123BAZ!', $fooSpy->bar());
        $fooSpy->assertCalls('baz')->calledOnce()->withoutArgs();
        $fooSpy->assertCalls('qux')->calledOnce()->withArgs(["xpto", 232]);
        $fooSpy->assertCalls('bar')->calledOnce()->withoutArgs();
    }

    public function testTestMockReflexaoDuasCriacoes() {
        $classMockName = MockTestUtil::createMock(Foo1::class);
        $fooSpy = new $classMockName('BAZ!', 123);
        $this->assertInstanceOf(Foo1::class, $fooSpy);

        $classMockName = MockTestUtil::createMock(Foo1::class);
        $fooSpy = new $classMockName('BAZ!', 123);
        $this->assertInstanceOf(Foo1::class, $fooSpy);
    }

    public function testTestMockMethod() {
        $mockName = MockTestUtil::createMock(Foo::class);
        $fooMock = new $mockName;

        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('qux')->calledOnce()->withArgs(['p1', 'p2'])->withReturn('QUX: p1 AND p2');

        $fooMock->setMockMethod('qux', function($arg1, $arg2) {
            return "Alterado $arg1 e $arg2";
        });
        $this->assertEquals('Alterado p1 e p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('qux')->getCall(1)->withArgs(['p1', 'p2'])->withReturn('Alterado p1 e p2');

        $fooMock->resetMethod('qux');
        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('qux')->calledOnce();
    }

}
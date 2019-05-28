<?php

namespace TestMock;

class Foo {
    protected function baz() {
        return 'BAZ!';
    }

    public function bar() {
        return $this->baz();
    }

    public function qux($param1, $param2) {
        return "QUX: $param1 AND $param2";
    }
}

class MockTestUtilTest extends \PHPUnit\Framework\TestCase {

    public function testTestMock() {
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

        $this->assertEquals('BAZ!', $fooMock->bar());
        $this->assertEquals('QUX: p1 AND p2', $fooMock->qux('p1', 'p2'));
        $fooMock->assertCalls('bar')->calledOnce()->withoutArgs();
        $fooMock->assertCalls('baz')->calledOnce()->withoutArgs();
        $fooMock->assertCalls('qux')->calledOnce()->withArgs(['p1', 'p2']);

        $fooMock->setMockReturn('baz', 'MODIFIED!');
        $this->assertEquals('MODIFIED!', $fooMock->bar());
        $this->assertEquals(2, $fooMock->assertCalls('bar')->callCount());
        $fooMock->assertCalls('baz')->getCall(1)->withoutArgs();
    }
}
<?php

namespace AuthorizationTest\TestUtil;

/**
 * Essa classe mixa a MockSpyTestUtil e é utilizada para criar um objeto de spy dinâmico.
 * 
 * @author Anderson Nogueira
 */
class MockSpyTestUtil {

    use MockTestUtil;

    public function __call($method, $args) {
        $this->addMockCall($method, $args);
        if (!array_key_exists($method, $this->_mockReturns)) {
            $method = array(get_parent_class($this), $method);
            return $this->_mockReturns[$method];
        }
    }

}

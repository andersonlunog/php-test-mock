<?php

namespace AuthorizationTest\TestUtil;

class TestUtil {
    
    public static function disableAuthentication($ctx) {
        $services = $ctx->getApplicationServiceLocator();
        $services->setAllowOverride(true);
        $config = $services->get('config');
        foreach ($config['guard'] as &$value) {
            foreach ($value as &$value1) {
                foreach ($value1 as $key2 => &$value2) {
                    $value1[$key2] = ['guest'];
                }
            }
        }
        $services->setService('config', $config);
        $services->setAllowOverride(false);
    }
}
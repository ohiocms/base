<?php

use Ohio\Core\Base\Helper\StrHelper;

class StrHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testisJson()
    {
        $this->assertFalse(StrHelper::isJson('not-json'));
        $this->assertTrue(StrHelper::isJson(json_encode('is-json')));
    }
}

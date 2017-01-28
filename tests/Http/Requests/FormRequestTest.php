<?php

use Ohio\Core\Http\Requests\FormRequest;
use Illuminate\Validation\Rules;

class FormRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Ohio\Core\Http\Requests\FormRequest::rules
     * @covers \Ohio\Core\Http\Requests\FormRequest::wantsJson
     * @covers \Ohio\Core\Http\Requests\FormRequest::authorize
     * @covers \Ohio\Core\Http\Requests\FormRequest::ruleExists
     */
    public function test()
    {
        $request = new FormRequest();

        $this->assertEquals([], $request->rules());
        $this->assertTrue($request->wantsJson());
        $this->assertTrue($request->authorize());
        $this->assertInstanceOf(Rules\Exists::class, $request->ruleExists('table', 'column', ['foo' => 'bar']));
    }

}
<?php

namespace Symbiote\MultiValueField\Tests;

use SilverStripe\Dev\SapphireTest;
use Symbiote\MultiValueField\Fields\MultiValueTextField;

/**
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueTextFieldTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        'MultiValueFieldTest_DataObject'
    ];

    public function testAttributesGeneration()
    {
        $field = MultiValueTextField::create('TestTextField', 'Test Text Field');
        $keySep = MultiValueTextField::KEY_SEP;

        $this->assertStringContainsString('id="' . $field->ID() . '"', $field->forTemplate());
        $this->assertStringContainsString('id="' . $field->ID() . $keySep . '0"', $field->forTemplate());
        $this->assertStringNotContainsString('id="' . $field->ID() . $keySep . '1"', $field->forTemplate());

        $field->setValue(['one']);
        $this->assertStringContainsString('id="' . $field->ID() . $keySep . '1"', $field->forTemplate());
        $this->assertStringNotContainsString('id="' . $field->ID() . $keySep . '2"', $field->forTemplate());

        $field->setValue(['one', 'two']);
        $this->assertStringContainsString('id="' . $field->ID() . $keySep . '2"', $field->forTemplate());
    }
}

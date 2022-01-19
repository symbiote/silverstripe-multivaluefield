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

        $this->assertContains('id="' . $field->ID() . '"', $field->forTemplate());
        $this->assertContains('id="' . $field->ID() . MultiValueTextField::KEY_SEP . '0"', $field->forTemplate());
        $this->assertNotContains('id="' . $field->ID() . MultiValueTextField::KEY_SEP . '1"', $field->forTemplate());

        $field->setValue(['one']);
        $this->assertContains('id="' . $field->ID() . MultiValueTextField::KEY_SEP . '1"', $field->forTemplate());
        $this->assertNotContains('id="' . $field->ID() . MultiValueTextField::KEY_SEP . '2"', $field->forTemplate());

        $field->setValue(['one', 'two']);
        $this->assertContains('id="' . $field->ID() . MultiValueTextField::KEY_SEP . '2"', $field->forTemplate());
    }
}

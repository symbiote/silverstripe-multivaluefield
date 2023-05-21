<?php

namespace Symbiote\MultiValueField\Tests;

use SilverStripe\Dev\SapphireTest;
use Symbiote\MultiValueField\Fields\MultiValueCheckboxField;

class MultiValueCheckboxFieldTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        MultiValueFieldTest_DataObject::class
    ];

    public function testLoadFrom()
    {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = ['One', 'Two'];
        $field = new MultiValueCheckboxField('MVField', 'MVField', ['One', 'Two', 'Three', 'Four']);
        $field->loadFrom($obj);
        $this->assertEquals('One,Two', $field->dataValue());
    }

    public function testSetValue()
    {
        $field = new MultiValueCheckboxField('MVField', 'MVField', ['One', 'Two', 'Three', 'Four']);
        $field->setValue(['One', 'Two']);
        $this->assertEquals('One,Two', $field->dataValue());
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = ['Three', 'Four'];
        $field->setValue('', $obj);
        $this->assertEquals('Three,Four', $field->dataValue());
    }
}

<?php
namespace SilverStripeAustralia\MultiValueField\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripeAustralia\MultiValueField\Fields\MultiValueField;


/**
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueFieldTest extends SapphireTest {

    protected static $extra_dataobjects = array(
        MultiValueFieldTest_DataObject::class
    );

    public function testUpdate() {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = ['One', 'Two'];
        $obj->write();

        $obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);
        $obj->MVField = ['Three'];
        $obj->write();

        $this->assertEquals(['Three'], $obj->obj('MVField')->getValues());
    }

    public function testSetArrayAsProperty() {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = ['One', 'Two'];
        $obj->write();

        $obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);
        $this->assertNotNull($obj->MVField);
        $this->assertEquals(['One', 'Two'], $obj->obj('MVField')->getValues());
    }

    public function testSetSerialisedStringAsProperty() {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = serialize(['One', 'Two']);
        $obj->write();

        $obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);
        $this->assertNotNull($obj->MVField);
        $this->assertEquals(['One', 'Two'], $obj->obj('MVField')->getValues());
    }

    public function testSetSerialisedStringWithSetValue() {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->obj('MVField')->setValue(serialize(['One', 'Two']));
        $obj->write();

        $obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);
        $this->assertNotNull($obj->MVField);
        $this->assertEquals(['One', 'Two'], $obj->obj('MVField')->getValues());
    }

    public function testSetArrayWithSetValue() {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->obj('MVField')->setValue(['One', 'Two']);
        $obj->write();

        $obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);
        $this->assertNotNull($obj->MVField);
        $this->assertEquals(['One', 'Two'], $obj->obj('MVField')->getValues());
    }

    public function testIsChanged() {
        $field = new MultiValueField();
        $this->assertFalse($field->isChanged());

        $field->setValue(['One', 'Two']);
        $this->assertTrue($field->isChanged());

        $field = new MultiValueField();
        $field->setValue(['One', 'Two'], null, false);
        $this->assertFalse($field->isChanged());

        $field = DBField::create_field('MultiValueField', ['One', 'Two']);
        $field->setValue(null);
        $this->assertTrue($field->isChanged());
    }

}

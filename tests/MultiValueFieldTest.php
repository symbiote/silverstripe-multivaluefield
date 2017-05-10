<?php
namespace SilverStripeAustralia\MultiValueField\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripeAustralia\MultiValueField\Fields\MultiValueField;


/**
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueFieldTest extends SapphireTest {

	protected static $extra_dataobjects = array(
        MultiValueFieldTest_DataObject::class
	);

	public function testMultiValueField() {
		$first = array('One', 'Two', 'Three');

		$obj = new MultiValueFieldTest_DataObject();
		$obj->MVField = $first;
		$obj->write();

		$this->assertTrue($obj->isInDB());
		$obj = MultiValueFieldTest_DataObject::get()->byID($obj->ID);

		$this->assertNotNull($obj->MVField);
		$this->assertEquals($first, $obj->MVField->getValues());

		$second = array('Four', 'Five');
		$obj->MVField = $second;
		$obj->write();

		$this->assertEquals($second, $obj->MVField->getValues());
	}

	public function testIsChanged() {
		$field = new MultiValueField();
		$this->assertFalse($field->isChanged());

		$field->setValue(array(1, 2, 3));
		$this->assertTrue($field->isChanged());

		$field = new MultiValueField();
		$field->setValue(array(1, 2, 3), null, false);
		$this->assertFalse($field->isChanged());

		$field = DBField::create_field('MultiValueField', array(1, 2, 3));
		$field->setValue(null);
		$this->assertTrue($field->isChanged());
	}

}

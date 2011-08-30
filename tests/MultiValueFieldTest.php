<?php
/**
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueFieldTest extends SapphireTest {

	public function testMultiValueField() {
		$first = array('One', 'Two', 'Three');

		$obj = new MultiValueFieldTest_DataObject();
		$obj->MVField = $first;
		$obj->write();

		$this->assertTrue($obj->isInDB());
		$obj = DataObject::get_by_id('MultiValueFieldTest_DataObject', $obj->ID);

		$this->assertNotNull($obj->MVField);
		$this->assertEquals($first, $obj->MVField->getValues());

		$second = array('Four', 'Five');
		$obj->MVField = $second;
		$obj->write();

		$this->assertEquals($second, $obj->MVField->getValues());
	}

}

/**
 * @ignore
 */
class MultiValueFieldTest_DataObject extends DataObject {

	public static $db = array(
		'MVField' => 'MultiValueField'
	);

}

<?php
/**
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 *
 * @mixin PHPUnit_Framework_TestCase
 */
class MultiValueFieldTest extends SapphireTest {

	protected $extraDataObjects = array(
		'MultiValueFieldTest_DataObject'
	);

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

    public function testSerializeIsNotUsed()
    {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->MVField = array(1, 2, 3);
        $obj->write();

        $query = new DataQuery('MultiValueFieldTest_DataObject');
        $query->where(array('"ID" = ?' => $obj->ID));
        $row = $query->execute()->first();
        $this->assertJson($row['MVFieldValue']);
	}

    public function testSerializeFallback()
    {
        $obj = new MultiValueFieldTest_DataObject();
        $obj->write();

        $array = array(1, 2, 3);

        $id = $obj->ID;

        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize($array)),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals($array, $obj->MVField->getValues());

        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize(array(1, new stdClass(), 3))),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals(array(), $obj->MVField->getValues());

        Config::nest();
        Config::inst()->update('MultiValueField', 'disable_unserialize', true);
        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize($array)),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals(array(), $obj->MVField->getValues());
        Config::unnest();

        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize(new stdClass())),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals(array(), $obj->MVField->getValues());

        $array = array(1, 'O:23:"Why would anyone write this', 3);
        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize($array)),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals($array, $obj->MVField->getValues());

        $array = array(1, ';O:23:"Why would anyone write this', 3);
        SQLUpdate::create(
            '"MultiValueFieldTest_DataObject"',
            array('"MVFieldValue"' => serialize($array)),
            array('"ID" = ?' => $id)
        )->execute();

        $obj = MultiValueFieldTest_DataObject::get()->byID($id);
        $this->assertEquals(array(), $obj->MVField->getValues());
    }

}

/**
 * @ignore
 */
class MultiValueFieldTest_DataObject extends DataObject implements TestOnly {

	private static $db = array(
		'MVField' => 'MultiValueField'
	);

}

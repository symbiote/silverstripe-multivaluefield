<?php

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class MultiValueCheckboxFieldTest extends SapphireTest
{
    public function testGetOptionsFromMultiValueFieldRelationship()
    {
        // Our stubbed object has a "join" method which returns a MultiValueField containing values
        $multiField = new MultiValueCheckboxField('FooField');
        $multiField->setName('multiRelation');

        // Create a mocked DataObject that returns a MultiValueField via a relation join getter
        $stub = $this->getMockBuilder(DataObject::class)
            ->setMethods(['multiRelation'])
            ->getMock();

        $returnedField = new MultiValueField('Foo');
        $returnedField->setValue(Member::get());

        $stub->expects($this->once())
            ->method('multiRelation')
            ->will($this->returnValue($returnedField));

        // Create a stub form which has the StubObject as its data record
        $form = new Form(new Controller(), 'StubForm', new FieldList(), new FieldList());
        $form->loadDataFrom($stub);
        $multiField->setForm($form);

        // Ensure that the returned result is a list and only contains members
        $result = $multiField->getOptions();
        $this->assertInstanceOf('SS_List', $result);
        $this->assertContainsOnlyInstancesOf('Member', $result);
    }
}

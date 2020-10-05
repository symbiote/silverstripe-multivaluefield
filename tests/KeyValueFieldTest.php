<?php

namespace Symbiote\KeyValueField\Tests;

use SilverStripe\Dev\SapphireTest;
use Symbiote\MultiValueField\Fields\KeyValueField;

class KeyValueFieldTest extends Sapphiretest
{
    public function testKeyFieldPlaceholder()
    {
        $field = new KeyValueField('test');
        $this->assertEmpty($field->getKeyFieldPlaceholder());
        $field->setKeyFieldPlaceholder('test-placeholder');
        $this->assertSame('test-placeholder', $field->getKeyFieldPlaceholder());
    }

    public function testValueFieldPlaceholder()
    {
        $field = new KeyValueField('test');
        $this->assertEmpty($field->getValueFieldPlaceholder());
        $field->setValueFieldPlaceholder('test-placeholder');
        $this->assertSame('test-placeholder', $field->getValueFieldPlaceholder());
    }

    public function testFieldContainsPlaceholder()
    {
        $field = new KeyValueField('test');
        $field->setKeyFieldPlaceholder('Key Placeholder');
        $field->setValueFieldPlaceholder('Value Placeholder');
        $html = $field->Field();
        $this->assertContains('placeholder="Key Placeholder"', $html);
        $this->assertContains('placeholder="Value Placeholder"', $html);
    }
}

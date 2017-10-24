<?php

namespace Symbiote\MultiValueField\ORM\FieldType;

use SilverStripe\Core\Convert;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * A DB field that serialises an array before writing it to the db, and returning the array
 * back to the end user.
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueField extends DBText
{
    /**
     *
     * @var boolean
     */
    protected $changed = false;

    /**
     * Returns the value of this field.
     * @return mixed
     */
    public function getValue()
    {
        // if we're not deserialised yet, do so
        if (is_string($this->value) && strlen($this->value) > 1) {
            // are we json encoded?
            if ($this->value{1} === ':') {
                $this->value = \unserialize($this->value);
            } else {
                $this->value = \json_decode($this->value, true);
            }
        }
        return $this->value;
    }

    public function getValues()
    {
        return $this->getValue();
    }

    /**
     * Overridden to make sure that the user_error that gets triggered if this is already is set
     * ... doesn't. DataObject tries setting this at times that it shouldn't :/.
     *
     * @param string $name
     */
    public function setName($name)
    {
        if (!$this->name) {
            parent::setName($name);
        }
    }

    /**
     * Set the value on the field.
     *
     * For a multivalue field, this will deserialise the value if it is a string
     *
     * @param mixed $value
     * @param array $record
     */
    public function setValue($value, $record = null, $markChanged = true)
    {
        if ($value && is_string($value) && strlen($value)) {
            if ($value{1} === ':') {
                $value = \unserialize($value);
            } else {
                $value = \json_decode($value, true);
            }
        }
        if ($markChanged) {
            $this->changed = $markChanged;
        }

        return parent::setValue($value, $record, $markChanged);
    }

    /**
     * (non-PHPdoc).
     *
     * @see core/model/fieldtypes/DBField#prepValueForDB($value)
     */
    public function prepValueForDB($value)
    {
        if ($value instanceof MultiValueField) {
            $value = $value->getValue();
        }
        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        }
        
        return parent::prepValueForDB($value);
    }

    public function isChanged()
    {
        return $this->changed;
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return new \Symbiote\MultiValueField\Fields\MultiValueTextField($this->name, $title);
    }

    /**
     * Convert to a textual list of items.
     */
    public function csv()
    {
        return $this->Implode(',');
    }

    /**
     * Return all items separated by a separator, defaulting to a comma and
     * space.
     *
     * @param string $separator
     *
     * @return string
     */
    public function Implode($separator = ', ')
    {
        return implode($separator, $this->getValue());
    }

    public function __toString()
    {
        if ($this->getValue()) {
            return $this->csv();
        }

        return '';
    }

    public function Items()
    {
        return $this->forTemplate();
    }

    public function forTemplate()
    {
        $items = [];
        if ($this->value) {
            foreach ($this->value as $key => $item) {
                $v = new DBVarchar('Value');
                $v->setValue($item);

                $obj     = new ArrayData([
                    'Value' => $v,
                    'Key' => $key,
                    'Title' => $item,
                ]);
                $items[] = $obj;
            }
        }

        return new ArrayList($items);
    }
}
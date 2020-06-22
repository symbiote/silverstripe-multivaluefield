<?php

namespace Symbiote\MultiValueField\ORM\FieldType;

use SilverStripe\ORM\FieldType\DBComposite;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * A DB field that serialises an array before writing it to the db, and returning the array
 * back to the end user.
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueField extends DBComposite
{
    /**
     * @param array
     */
    private static $composite_db = array(
        "Value" => "Text",
    );

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
        $value = $this->value;
        if (is_null($value)) {
            $value = $this->getField('Value');
        }
        $this->value = is_string($value) ? $this->unserializeData($value) : $value;
        return $this->value;
    }

    public function getValues()
    {
        return $this->getValue();
    }

    /**
     * Set the value on the field. Ensures the underlying composite field
     * logic that looks for Value will trigger if the value set is
     *
     *
     * For a multivalue field, this will deserialise the value if it is a string
     *
     * @param mixed $value
     * @param array $record
     * @return $this
     */
    public function setValue($value, $record = null, $markChanged = true)
    {
        $this->changed = $this->changed || $markChanged;
        if (!is_null($value)) {
            // so that subsequent getValue calls re-load the value item correctly
            $this->value = null;
            if (!is_string($value)) {
                $value = $this->serializeValue($value);
            }
            $value = ['Value' => $value];
        }
        return parent::setValue($value, $record, $markChanged);
    }

    /**
     * Serializes a value object to a json string
     *
     * @param array|object $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_object($value) || is_array($value)) {
            return json_encode($value);
        }
    }

    /**
     * Unserialises data, depending on new or old format
     *
     * @param string $data
     *
     * @return array
     */
    protected function unserializeData($data)
    {
        $value = null;
        // if we're not deserialised yet, do so
        if (is_string($data) && strlen($data) > 1) {
            // are we json encoded?
            if ($data[1] === ':') {
                $value = \unserialize($data);
            } else {
                $value = \json_decode($data, true);
            }
        }
        return $value;
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
    
    public function ItemsByKey()
    {
        $value = $this->getValue();
        if(array_keys($value) == range(0, count($value) - 1))
            $value = [];
        return new ArrayData($value);
    }

    public function Items()
    {
        return $this->forTemplate();
    }

    public function forTemplate()
    {
        $items = [];
        $value = $this->getValue();
        if ($value) {
            foreach ($value as $key => $item) {
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

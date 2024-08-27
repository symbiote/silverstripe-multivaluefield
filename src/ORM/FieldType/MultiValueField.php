<?php

namespace Symbiote\MultiValueField\ORM\FieldType;

use SilverStripe\Forms\FormField;
use SilverStripe\ORM\FieldType\DBComposite;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;
use Symbiote\MultiValueField\Fields\MultiValueTextField;

/**
 * A DB field that serialises an array before writing it to the db, and returning the array
 * back to the end user.
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueField extends DBComposite
{
    private static array $composite_db = [
        "Value" => "Text",
    ];

    protected bool $changed = false;

    /**
     * Returns the value of this field.
     */
    public function getValue(): mixed
    {
        $value = $this->value;
        if (is_null($value)) {
            $value = $this->getField('Value');
        }
        $this->value = is_string($value) ? $this->unserializeData($value) : $value;
        return $this->value;
    }

    public function getValues(): mixed
    {
        return $this->getValue();
    }

    /**
     * Set the value on the field. Ensures the underlying composite field
     * logic that looks for Value will trigger if the value set is
     *
     * For a multivalue field, this will deserialise the value if it is a string
     */
    public function setValue(mixed $value, null|array|ViewableData $record = null, bool $markChanged = true): static
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
     */
    protected function unserializeData(mixed $data): mixed
    {
        $value = [];
        // if we're not deserialised yet, do so
        if (is_string($data) && strlen($data ?? '') > 1) {
            // are we json encoded?
            if ($data[1] === ':') {
                $value = \unserialize($data ?? '');
            } else {
                $value = \json_decode($data ?? '', true);
            }
        }
        return $value;
    }

    public function prepValueForDB(mixed $value): mixed
    {
        if ($value instanceof MultiValueField) {
            $value = $value->getValue();
        }
        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        }

        return parent::prepValueForDB($value);
    }

    public function isChanged(): bool
    {
        return $this->changed;
    }

    public function scaffoldFormField(?string $title = null, array $params = []): FormField
    {
        return new MultiValueTextField($this->name, $title);
    }

    /**
     * Convert to a textual list of items.
     */
    public function csv(): string
    {
        return $this->Implode(',');
    }

    /**
     * Return all items separated by a separator, defaulting to a comma and
     * space.
     */
    public function Implode(string $separator = ', '): string
    {
        return implode($separator ?? '', $this->getValue());
    }

    public function __toString(): string
    {
        if ($this->getValue()) {
            return $this->csv();
        }

        return '';
    }

    public function ItemByKey(): ArrayData
    {
        $values = $this->getValue();
        if (array_keys($values ?? []) == range(0, count($values ?? []) - 1)) {
            $values = [];
        }
        return new ArrayData($values);
    }

    public function Items(): ArrayList
    {
        $items = [];
        $value = $this->getValue();
        if ($value) {
            foreach ($value as $key => $item) {
                $v = new DBVarchar('Value');
                $v->setValue($item);

                $obj = new ArrayData([
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

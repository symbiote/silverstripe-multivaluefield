<?php

namespace Symbiote\MultiValueField\Fields;

use Symbiote\MultiValueField\ORM\FieldType\MultiValueField;

use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataObject;

/**
 * A checkboxset that uses a multivalue field for key / val pairs
 *
 * @author marcus@symbiote.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class MultiValueCheckboxField extends CheckboxSetField
{
    protected $disabled = false;

    /**
     * @var array
     */
    protected $defaultItems = [];

    /**
     * Do we store keys + values or just the values?
     *
     * @var boolean
     */
    protected $storeKeys = false;

    /**
     * @todo Explain different source data that can be used with this field,
     * e.g. SQLMap, DataObjectSet or an array.
     *
     * @todo Should use CheckboxField FieldHolder rather than constructing own markup.
     */
    public function Field($properties = [])
    {
        // Requirements::css(SAPPHIRE_DIR . '/css/CheckboxSetField.css');

        $source = $this->source;
        $values = $this->value;
        if ($values instanceof MultiValueField) {
            $values = $values->getValues();

            if ($this->storeKeys && is_array($values)) {
                // use the keys instead, as that's how we've stored things
                $values = array_keys($values ?? []);
            }
        }

        // Get values from the join, if available
        if (is_object($this->form)) {
            $record = $this->form->getRecord();
            if (!$values && $record && $record->{$this->name}) {
                $prop = $record->{$this->name};
                if ($prop && $prop instanceof MultiValueField) {
                    $values = $prop->getValues();
                }
            }
        }

        // Source is not an array
        if (!is_array($source) && !is_a($source, 'SQLMap')) {
            if (is_array($values)) {
                $items = $values;
            } else {
                // Source and values are DataObject sets.
                if ($values && is_a($values, 'DataObjectSet')) {
                    foreach ($values as $object) {
                        if (is_a($object, DataObject::class)) {
                            $items[] = $object->ID;
                        }
                    }
                } elseif ($values && is_string($values)) {
                    $items = explode(',', $values ?? '');
                    $items = str_replace('{comma}', ',', $items ?? '');
                }
            }
        } else {
            // Sometimes we pass a singluar default value thats ! an array && !DataObjectSet
            if (is_a($values, 'DataObjectSet') || is_array($values)) {
                $items = $values;
            } else {
                $items = explode(',', $values ?? '');
                $items = str_replace('{comma}', ',', $items ?? '');
            }
        }

        if (is_array($source)) {
            unset($source['']);
        }

        $odd     = 0;
        $options = '';

        if ($source == null) {
            $source  = [];
            $options = "<li>"
                ._t('MultiValueCheckboxField.NoOptions', 'No options available')
                ."</li>";
        }

        if ($source) {
            foreach ($source as $index => $item) {
                if (is_a($item, DataObject::class)) {
                    $key   = $item->ID;
                    $value = $item->Title;
                } else {
                    $key   = $index;
                    $value = $item;
                }

                    $odd        = ($odd + 1) % 2;
                    $extraClass = $odd ? 'odd' : 'even';
                    $extraClass .= ' val'.str_replace(' ', '', $key ?? '');
                    $itemID     = $this->id().'_'.preg_replace('/[^a-zA-Z0-9]+/', '', $key ?? '');
                    $checked    = '';
                if (isset($items)) {
                    $checked = (in_array($key, $items ?? []) || in_array($key, $this->defaultItems ?? []))
                        ? ' checked="checked"' : '';
                }

                    $disabled = ($this->disabled || in_array($key, $this->disabledItems ?? []))
                        ? $disabled = ' disabled="disabled"'
                : '';
                    $options .= "<li class=\"$extraClass\"><input id=\"$itemID\" name=\"$this->name[$key]\""
                        . " type=\"checkbox\" value=\"$key\"$checked $disabled class=\"checkbox\" /> <label"
                        . " for=\"$itemID\">$value</label></li>\n";
            }
        }

        return "<ul id=\"{$this->id()}\" class=\"optionset checkboxsetfield{$this->extraClass()}\">\n$options</ul>\n";
    }

    public function setDisabled($val)
    {
        $this->disabled = $val;
    }

    /**
     * Default selections, regardless of the {@link setValue()} settings.
     * Note: Items marked as disabled through {@link setDisabledItems()} can still be
     * selected by default through this method.
     *
     * @param array $items Collection of array keys, as defined in the $source array
     */
    public function setDefaultItems($items)
    {
        $this->defaultItems = $items;
    }

    /**
     * Do we store keys and values?
     *
     * @param boolean $val
     */
    public function setStoreKeys($val)
    {
        $this->storeKeys = $val;
        return $this;
    }

    /**
     * @return Array
     */
    public function getDefaultItems()
    {
        return $this->defaultItems;
    }

    /**
     * Save the current value of this CheckboxSetField into a DataObject.
     * If the field it is saving to is a has_many or many_many relationship,
     * it is saved by setByIDList(), otherwise it creates a comma separated
     * list for a standard DB text/varchar field.
     *
     * @param DataObject $record The record to save into
     */
    public function saveInto(DataObjectInterface $record)
    {
        $fieldname = $this->name;
        if ($fieldname && $record) {
            if ($this->value) {
                if ($this->storeKeys) {
                    $vals = $this->getSource();
                    if (!is_array($this->value)) {
                        $this->value = array($this->value);
                    }
                    foreach ($this->value as $selected) {
                        if (isset($vals[$selected])) {
                            $this->value[$selected] = $vals[$selected];
                        }
                    }
                }

                $record->$fieldname = $this->value;

//              $this->value = str_replace(',', '{comma}', $this->value);
//              $record->$fieldname = $this->value;
            } else {
                $record->$fieldname = [];
            }
        }
    }

    /**
     * Load the value from the dataobject into this field
     *
     * @param DataObject|DataObjectInterface $record
     */
    public function loadFrom(DataObjectInterface $record)
    {
        $fieldName = $this->getName();
        if (empty($fieldName) || empty($record)) {
            return;
        }

        if ($record->hasField($fieldName)) {
            $value = $record->$fieldName;

            parent::setValue($value);
        }
    }

    /**
     * Return the CheckboxSetField value as a string
     * selected item keys.
     *
     * @return string
     */
    public function dataValue()
    {
        if ($this->value && is_array($this->value)) {
            $filtered = [];
            foreach ($this->value as $item) {
                if ($item) {
                    $filtered[] = str_replace(",", "{comma}", $item ?? '');
                }
            }

            return implode(',', $filtered);
        }

        return '';
    }

    public function performDisabledTransformation()
    {
        $clone = clone $this;
        $clone->setDisabled(true);

        return $clone;
    }

    /**
     * Transforms the source data for this CheckboxSetField
     * into a comma separated list of values.
     *
     * @return ReadonlyField
     */
    public function performReadonlyTransformation()
    {
        $values = '';
        $data   = [];

        $items = $this->value;
        if ($this->source) {
            foreach ($this->source as $source) {
                if (is_object($source)) {
                    $sourceTitles[$source->ID] = $source->Title;
                }
            }
        }

        if ($items) {
            // Items is a DO Set
            if (is_a($items, 'DataList')) {
                foreach ($items as $item) {
                    $data[] = $item->Title;
                }
                if ($data) {
                    $values = implode(', ', $data);
                }

                // Items is an array or single piece of string (including comma seperated string)
            } else {
                if (!is_array($items)) {
                    $items = preg_split('/ *, */', trim($items ?? ''));
                }

                foreach ($items as $item) {
                    if (is_array($item)) {
                        $data[] = $item['Title'];
                    } elseif (is_array($this->source) && !empty($this->source[$item])) {
                        $data[] = $this->source[$item];
                    } elseif (is_a($this->source, 'DataList')) {
                        $data[] = $sourceTitles[$item];
                    } else {
                        $data[] = $item;
                    }
                }

                $values = implode(', ', $data);
            }
        }

        $title = ($this->title) ? $this->title : '';

        $field = new ReadonlyField($this->name, $title, $values);
        $field->setForm($this->form);

        return $field;
    }

    public function ExtraOptions()
    {
        return FormField::ExtraOptions();
    }
}

<?php

/**
 * A checkboxset that uses a multivalue field for key / val pairs
 *
 * @author marcus@symbiote.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class MultiValueCheckboxField extends CheckboxSetField {
	/**
	 * Do we store keys + values or just the values?
	 *
	 * @var boolean
	 */
	protected $storeKeys = false;

	public function Field($properties = array())
    {
        Requirements::css(FRAMEWORK_DIR . '/css/CheckboxSetField.css');

        $this->addExtraClass('checkboxsetfieldoptionset');

        return parent::Field($properties);
    }

    /**
     * Overloaded from the parent CheckboxSetField::getOptions in order to handle MultiValueField
     * values and relationship field values. The customised part of this function are annotated
     * with "Custom" comment lines, and the rest of copied through.
     *
     * {@inheritDoc}
     */
    public function getOptions()
    {
		$source = $this->source;
		$values = $this->value;
		$items = array();

		// Custom: handle MultiValueField values
		if ($values instanceof MultiValueField) {
			$values = $values->getValues();

			if ($this->storeKeys && is_array($values)) {
				// use the keys instead, as that's how we've stored things
				$values = array_keys($values);
			}
		}

		// Get values from the join, if available
		if (is_object($this->form)) {
            $record = $this->form->getRecord();
            if (!$values && $record && $record->hasMethod($this->name)) {
                $funcName = $this->name;
                $join = $record->$funcName();
                if ($join) {
                    // Custom: Handle MultiValueField relations
                    if ($join instanceof MultiValueField) {
                        $values = $join->getValues();
                    } else {
                        // Default core logic
                        foreach ($join as $joinItem) {
                            $values[] = $joinItem->ID;
                        }
                    }
                }
			}
		}

		// Source is not an array
        if (!is_array($source)) {
            if (is_array($values)) {
                $items = $values;
            } else {
                // Source and values are DataObject sets.
                if ($values && $values instanceof SS_List) {
                    foreach ($values as $object) {
                        if ($object instanceof DataObject) {
                            $items[] = $object->ID;
                        }
                    }
                } elseif ($values && is_string($values)) {
                    if (!empty($values)) {
                        $items = explode(',', $values);
                        $items = str_replace('{comma}', ',', $items);
                    } else {
                        $items = array();
                    }
                }
            }
        } else {
            // Sometimes we pass a singluar default value thats ! an array && !SS_List
            if ($values instanceof SS_List || is_array($values)) {
                $items = $values;
            } else {
                if ($values === null) {
                    $items = array();
                } else {
                    if (!empty($values)) {
                        $items = explode(',', $values);
                        $items = str_replace('{comma}', ',', $items);
                    } else {
                        $items = array();
                    }
                }
            }
        }

		if (is_array($source)) {
			unset($source['']);
		}

		$options = array();

		if ($source == null) {
			$source = array();
		}

		// See CheckboxSetField::getOptions from here on
        $odd = false;
		foreach ($source as $index => $item) {
            // Ensure $title is cast for template
            if ($item instanceof DataObject) {
                $value = $item->ID;
                $title = $item->obj('Title');
            } elseif ($item instanceof DBField) {
                $value = $index;
                $title = $item;
            } else {
                $value = $index;
                $title = DBField::create_field('Text', $item);
            }

            $itemID = $this->ID() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $value);
            $extraClass = $odd ? 'odd' : 'even';
            $odd = !$odd;
            $extraClass .= ' val' . preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $value);

            $options[] = ArrayData::create(array(
                'ID' => $itemID,
                'Class' => $extraClass,
                'Name' => "{$this->name}[{$value}]",
                'Value' => $value,
                'Title' => $title,
                'isChecked' => in_array($value, $items) || in_array($value, $this->defaultItems),
                'isDisabled' => $this->disabled || in_array($value, $this->disabledItems)
            ));
		}

        $options = ArrayList::create($options);

        $this->extend('updateGetOptions', $options);

        return $options;
	}

	/**
	 * Do we store keys and values?
	 *
	 * @param boolean $val
	 */
	public function setStoreKeys($val) {
		$this->storeKeys = $val;
		return $this;
	}

	/**
	 * Save the current value of this CheckboxSetField into a DataObject.
	 * If the field it is saving to is a has_many or many_many relationship,
	 * it is saved by setByIDList(), otherwise it creates a comma separated
	 * list for a standard DB text/varchar field.
	 *
	 * @param DataObject $record The record to save into
	 */
	public function saveInto(DataObjectInterface $record) {

		$fieldname = $this->name ;
		if($fieldname && $record) {
			if($this->value) {
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

//				$this->value = str_replace(',', '{comma}', $this->value);
//				$record->$fieldname = $this->value;
			} else {
				$record->$fieldname = array();
			}
		}
	}
}

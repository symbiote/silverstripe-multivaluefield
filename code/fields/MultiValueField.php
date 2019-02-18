<?php

/**
 * A DB field that serialises an array before writing it to the db, and returning the array
 * back to the end user.
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueField extends DBField implements CompositeDBField
{

    /**
     * As blindly unserialising unknown values is a major security risk you might wish to disable unserialising with
     * this config setting.
     *
     * Note that this field does not serialise values with PHP any more so new fields should never use `unserialize`
     *
     * @var bool
     */
    private static $disable_serialise = false;

	protected $changed = false;

	/**
	 * @param array
	 */
	static $composite_db = array(
		"Value" => "Text",
	);

	/**
	 * Returns the value of this field.
	 * @return mixed
	 */
	public function getValue() {
		// if we're not deserialised yet, do so
        $this->value = $this->restoreValueFromDb($this->value);
		return $this->value;
	}

	public function getValues() {
		return $this->getValue();
	}

	/**
	 * Overridden to make sure that the user_error that gets triggered if this is already is set
	 * ... doesn't. DataObject tries setting this at times that it shouldn't :/
	 *
	 * @param string $name
	 */
	public function setName($name) {
		if (!$this->name) {
			parent::setName($name);
		}
	}

	/**
	 * Set the value on the field.
	 *
	 * For a multivalue field, this will deserialise the value if it is a string
	 * @param mixed $value
	 * @param array $record
	 */
	public function setValue($value, $record = null, $markChanged = true) {
		if ($markChanged) {
			if (is_array($value)) {
				$this->changed = true;
			} else if (is_object($value)) {
				$value = isset($value->value) && is_array($value->value) ? $value->value : array();
				$this->changed = true;
			} else if (!$value) {
				$value   = array();
				$this->changed = true;
			}
		} elseif (!is_array($value) && $record && isset($record[$this->name.'Value'])) {
			$value = $record[$this->name.'Value'];
		}

        $value = $this->restoreValueFromDb($value);

        $this->changed = $this->changed || $markChanged;

		return parent::setValue($value);
	}

	/**
	 * (non-PHPdoc)
	 * @see core/model/fieldtypes/DBField#prepValueForDB($value)
	 */
	public function prepValueForDB($value) {
		if (!$this->nullifyEmpty && $value === '') {
			return "'" . Convert::raw2sql($value) . "'";
		} else {
			if ($value instanceof MultiValueField) {
				$value = $value->getValue();
			}
			if (is_object($value)) {
                throw new LogicException(__CLASS__ . ' values must not be objects');
            }
			if (is_array($value)) {
				$value = Convert::array2json($value);
			}
			return parent::prepValueForDB($value);
		}
	}

	public function requireField() {
		$parts=Array('datatype'=>'mediumtext', 'character set'=>'utf8', 'collate'=>'utf8_general_ci', 'arrayValue'=>$this->arrayValue);
		$values=Array('type'=>'text', 'parts'=>$parts);
		DB::requireField($this->tableName, $this->name . 'Value', $values);
	}

	public function compositeDatabaseFields() {
		return self::$composite_db;
	}

	public function writeToManipulation(&$manipulation) {
		if($this->getValue()) {
			$manipulation['fields'][$this->name.'Value'] = $this->prepValueForDB($this->getValue());
		} else {
			$manipulation['fields'][$this->name.'Value'] = DBField::create_field('Text', $this->getValue())->nullValue();
		}
	}

	public function addToQuery(&$query) {
		parent::addToQuery($query);
		$name = sprintf('%sValue', $this->name);
		$val = sprintf('"%sValue"', $this->name);
		$select = $query->getSelect();
		if (!isset($select[$name])) {
			$query->addSelect(array($name => $val));
		}
	}

	public function isChanged() {
		return $this->changed;
	}

	public function scaffoldFormField($title = null) {
		return new MultiValueTextField($this->name, $title);
	}

	/**
	 * Convert to a textual list of items
	 */
	public function csv() {
		return $this->Implode(',');
	}

	/**
	 * Return all items separated by a separator, defaulting to a comma and
	 * space.
	 *
	 * @param string $separator
	 * @return string
	 */
	public function Implode($separator = ', ') {
		return implode($separator, $this->getValue());
	}

    public function __toString() {
        if ($this->getValue()) {
            return $this->csv();
        }
        return '';
    }

	public function Items() {
		return $this->forTemplate();
	}

	public function forTemplate() {
		$items = array();
		if ($this->value) {
			foreach ($this->value as $key => $item) {
				$v = new Varchar('Value');
				$v->setValue($item);

				$obj = new ArrayData(array(
					'Value' => $v,
					'Key'	=> $key,
					'Title' => $item
				));
				$items[] = $obj;
			}
		}

		return new ArrayList($items);
	}

    /**
     * @param mixed $value
     * @return array
     */
    protected function restoreValueFromDb($value)
    {
        if ($value && is_string($value)) {
            $decoded = Convert::json2array($value);

            if (
                $decoded === null &&
                $value !== 'null' &&
                !self::config()->get('disable_unserialize') &&
                strpos($value, 'a:') === 0 &&
                !preg_match('/(^|;|{)O:\d+:"/', $value)
            ) {
                $value = unserialize($value);
            } else {
                $value = $decoded;
            }
        }
        return $value ?: array();
    }

    public function scalarValueOnly()
    {
        return false;
    }
}

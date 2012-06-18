<?php

/**
 * A DB field that serialises an array before writing it to the db, and returning the array
 * back to the end user. 
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueField extends DBField implements CompositeDBField {
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
	function getValue() {
		// if we're not deserialised yet, do so
		if ($this->exists() && is_string($this->value)) {
			$this->value = unserialize($this->value);
		}
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
	function setValue($value, $record = null, $markChanged = true) {
		if ($markChanged) {
			if (is_array($value)) {
				$this->value = $value;
				$this->changed = true;
			} else if (is_object($value)) {
				$this->value = isset($value->value) && is_array($value->value) ? $value->value : array();
				$this->changed = true;
			} else if (!$value) {
				$this->value   = array();
				$this->changed = true;
			}
			return;
		}

		if (!is_array($value) && $record && isset($record[$this->name.'Value'])) {
			$value = $record[$this->name.'Value'];
		}

		if ($value && is_string($value)) {
			$this->value = unserialize($value);
		} else if ($value) {
			$this->value = $value;
		}

		$this->changed = $this->changed || $markChanged;
	}

	/**
	 * (non-PHPdoc)
	 * @see core/model/fieldtypes/DBField#prepValueForDB($value)
	 */
	function prepValueForDB($value) {
		if (!$this->nullifyEmpty && $value === '') {
			return "'" . Convert::raw2sql($value) . "'";
		} else {
			if ($value instanceof MultiValueField) {
				$value = $value->getValue();
			}
			if (is_object($value) || is_array($value)) {
				$value = serialize($value);
			}
			return parent::prepValueForDB($value);
		}
	}
	
	function requireField() {
		$parts=Array('datatype'=>'mediumtext', 'character set'=>'utf8', 'collate'=>'utf8_general_ci', 'arrayValue'=>$this->arrayValue);
		$values=Array('type'=>'text', 'parts'=>$parts);
		DB::requireField($this->tableName, $this->name . 'Value', $values);
	}

	function compositeDatabaseFields() {
		return self::$composite_db;
	}

	function writeToManipulation(&$manipulation) {
		if($this->getValue()) {
			$manipulation['fields'][$this->name.'Value'] = $this->prepValueForDB($this->getValue());
		} else {
			$manipulation['fields'][$this->name.'Value'] = DBField::create_field('Text', $this->getValue())->nullValue();
		}
	}

	function addToQuery(&$query) {
		parent::addToQuery($query);
		$name = sprintf('%sValue', $this->name);
		$val = sprintf('"%sValue"', $this->name);
		$select = $query->getSelect();
		if (!isset($select[$name])) {
			$query->addSelect(array($name => $val)); 
		}
	}

	function isChanged() {
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
	 * @param  string $separator
	 * @return string
	 */
	public function Implode($separator = ', ') {
		return implode($separator, $this->getValue());
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
}

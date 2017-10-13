<?php

/**
 * A field that lets you specify both a key AND a value for each row entry
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class KeyValueField extends MultiValueTextField {
	protected $sourceKeys;
	protected $sourceValues;

	public function __construct($name, $title = null, $sourceKeys = array(), $sourceValues = array(), $value=null, $form=null) {
		parent::__construct($name, ($title===null) ? $name : $title, $value, $form);
		$this->sourceKeys = $sourceKeys;
		$this->sourceValues = $sourceValues;
	}

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript('multivaluefield/javascript/multivaluefield.js');
		Requirements::css('multivaluefield/css/multivaluefield.css');

		$nameKey = $this->name . '[key][]';
		$nameVal = $this->name . '[val][]';
		$fields = array();

		if ($this->value) {
			foreach ($this->value as $i => $v) {
				if ($this->readonly) {
					$fieldAttr = array(
						'class' => 'mventryfield  mvkeyvalReadonly ' . ($this->extraClass() ? $this->extraClass() : ''),
						'id' => $this->id().MultiValueTextField::KEY_SEP.$i,
						'name' => $nameKey,
						'tabindex' => $this->getAttribute('tabindex')
					);

					$keyField = self::create_tag('span', $fieldAttr, Convert::raw2xml($i));
					$fieldAttr['id'] = $this->id().MultiValueTextField::KEY_SEP.$v;
					$valField = self::create_tag('span', $fieldAttr, Convert::raw2xml($v));
					$fields[] = $keyField . $valField;
				} else {
					$keyField = $this->createSelectList($i, $nameKey, $this->sourceKeys, $i);
					$valField = $this->createSelectList($i, $nameVal, $this->sourceValues, $v);
					$fields[] = $keyField . ' ' . $valField;
				}
			}
		} else {
			$i = -1;
		}

		if (!$this->readonly) {
			$keyField = $this->createSelectList('new', $nameKey, $this->sourceKeys);
			$valField = $this->createSelectList('new', $nameVal, $this->sourceValues);
			$fields[] = $keyField . ' ' . $valField;
//			$fields[] = $this->createSelectList('new', $name, $this->source);
		}

		return '<ul id="'.$this->id().'" class="multivaluefieldlist mvkeyvallist '.$this->extraClass().'"><li>'.implode('</li><li>', $fields).'</li></ul>';
	}

	protected function createSelectList($number, $name, $values, $selected = '') {
		$options = self::create_tag(
			'option',
			array(
				'selected' => $selected == '' ? 'selected' : '',
				'value' => ''
			),
			''
		);

		foreach ($values as $index => $title) {
			$attrs = array('value'=>$index);
			if ($index == $selected) {
				$attrs['selected'] = 'selected';
			}
			$options .= self::create_tag('option', $attrs, Convert::raw2xml($title));
		}

		if (count($values)) {
			$attrs = array(
				'class' => 'text mventryfield mvdropdown ' . ($this->extraClass() ? $this->extraClass() : ''),
				'id' => $this->id().MultiValueTextField::KEY_SEP.$number,
				'name' => $name,
				'tabindex' => $this->getAttribute('tabindex')
			);

			if($this->disabled) $attrs['disabled'] = 'disabled';

			return self::create_tag('select', $attrs, $options);
		} else {
			$attrs = array(
				'class' => 'text mventryfield mvtextfield ' . ($this->extraClass() ? $this->extraClass() : ''),
				'id' => $this->id().MultiValueTextField::KEY_SEP.$number,
				'value' => $selected,
				'name' => $name,
				'tabindex' => $this->getAttribute('tabindex'),
				'type'	=> 'text',
			);

			if($this->disabled) $attrs['disabled'] = 'disabled';

			return self::create_tag('input', $attrs);
		}
	}

	public function setValue($v) {
		if (is_array($v)) {
			// we've been set directly via the post - lets convert things to an appropriate key -> value
			// structure
			if (isset($v['key'])) {
				$newVal = array();

				for ($i = 0, $c = count($v['key']); $i < $c; $i++) {
					if (strlen($v['key'][$i]) && strlen($v['val'][$i])) {
						$newVal[$v['key'][$i]] = $v['val'][$i];
					}
				}

				$v = $newVal;
			}
		}

 		if ($v instanceof MultiValueField) {
			$v = $v->getValues();
		}

		if (!is_array($v)) {
			$v = array();
		}

		return parent::setValue($v);
	}
}

<?php
/**
 * A multivalued field that uses dropdown boxes for selecting the value for each
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueDropdownField extends MultiValueTextField {
	protected $source;

	public function __construct($name, $title = null, $source = array(), $value=null, $form=null) {
		parent::__construct($name, ($title===null) ? $name : $title, $value, $form);
		$this->source = $source;
	}

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-livequery/jquery.livequery.js');
		Requirements::javascript('multivaluefield/javascript/multivaluefield.js');
		
		$name = $this->name . '[]';
		$fields = array();

		
		if ($this->value) {
			foreach ($this->value as $i => $v) {
				if ($this->readonly) {
					$fieldAttr = array(
						'class' => 'mventryfield  mvdropdownReadonly ' . ($this->extraClass() ? $this->extraClass() : ''),
						'id' => $this->id().':'.$i,
						'name' => $name,
						'tabindex' => $this->getTabIndex()
					);
					$fields[] = $this->createTag('span', $fieldAttr, Convert::raw2xml($v));
				} else {
					$fields[] = $this->createSelectList($i, $name, $this->source, $v);
				}
			}
		} else {
			$i = -1;
		}

		if (!$this->readonly) {
			$fields[] = $this->createSelectList($i + 1, $name, $this->source);
		}

		return '<ul id="'.$this->id().'" class="multivaluefieldlist '.$this->extraClass().'"><li>'.implode('</li><li>', $fields).'</li></ul>';
	}

	protected function createSelectList($number, $name, $values, $selected = '') {
		$options = $this->createTag(
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
			$options .= $this->createTag('option', $attrs, Convert::raw2xml($title));
		}

		$attrs = array(
			'class' => 'mventryfield mvdropdown ' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id().':'.$number,
			'name' => $name,
			'tabindex' => $this->getAttribute('tabindex')
		);

		if($this->disabled) $attrs['disabled'] = 'disabled';

		return $this->createTag('select', $attrs, $options);
	}
}
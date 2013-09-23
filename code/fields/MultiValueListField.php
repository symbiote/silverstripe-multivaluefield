<?php

/**
 * A multivalued field that uses a multi choice select box for selecting the value 
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueListField extends MultiValueTextField {
	protected $source;

	public function __construct($name, $title = null, $source = array(), $value=null, $form=null) {
		parent::__construct($name, ($title===null) ? $name : $title, $value, $form);
		$this->source = $source;
	}

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
		Requirements::javascript('multivaluefield/javascript/multivaluefield.js');
		Requirements::css('multivaluefield/css/multivaluefield.css');
		
		$name = $this->name . '[]';

		$options = '';
		if (!$this->value) {
			$this->value = array();
		}

		foreach ($this->source as $index => $title) {
			$attrs = array('value'=>$index);
			if (in_array($index, $this->value)) {
				$attrs['selected'] = 'selected';
			}
			$options .= $this->createTag('option', $attrs, Convert::raw2xml($title));
		}

		$attrs = array(
			'class' => 'mventryfield mvlistbox ' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id(),
			'name' => $name,
			'tabindex' => $this->getAttribute('tabindex'),
			'multiple' => 'multiple',
		);

		if($this->disabled) $attrs['disabled'] = 'disabled';

		return $this->createTag('select', $attrs, $options);
	}
}
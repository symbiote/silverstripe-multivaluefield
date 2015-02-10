<?php

/**
 * A text field for multivalued text entry
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueTextField extends FormField {

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript('multivaluefield/javascript/multivaluefield.js');
		Requirements::css('multivaluefield/css/multivaluefield.css');

		$name = $this->name . '[]';
		$fields = array();

		$attributes = array(
			'type' => 'text',
			'class' => 'text mvtextfield mventryfield ' . ($this->extraClass() ? $this->extraClass() : ''),
			// 'id' => $this->id(),
			'name' => $name,
			// 'value' => $this->Value(),
		);

		if($this->disabled) $attributes['disabled'] = 'disabled';

		$fieldAttr = $attributes;
		if ($this->value) {
			foreach ($this->value as $i => $v) {
				$fieldAttr['id'] = $this->id().':'.$i;
				$fieldAttr['value'] = $v;
				if ($this->readonly) {
					unset($fieldAttr['value']);
					$fields[] = $this->createReadonlyInput($fieldAttr, $v);
				} else {
					$fields[] = $this->createInput($fieldAttr);
				}
			}
		}

		if (!$this->readonly) {
			$fields[] = $this->createInput($attributes);
		}

		if (count($fields)) {
			return '<ul id="'.$this->id().'" class="multivaluefieldlist '.$this->extraClass().'"><li>'.implode('</li><li>', $fields).'</li></ul>';
		} else {
			return '<div id="'.$this->id().'" class="multivaluefieldlist '.$this->extraClass().'"></div>';
		}
	}

	public function createReadonlyInput($attributes, $value) {
		return self::create_tag('span', $attributes, Convert::raw2xml($value));
	}

	public function createInput($attributes) {
		return self::create_tag('input', $attributes);
	}

	public function  performReadonlyTransformation() {
		$new = clone $this;
		$new->setReadonly(true);
		return $new;
	}

	public function setValue($v) {
		if (is_array($v)) {
			// we've been set directly via the post - lets prune any empty values
			foreach ($v as $key => $val) {
				if (!strlen($val)) {
					unset($v[$key]);
				}
			}
		}
 		if ($v instanceof MultiValueField) {
			$v = $v->getValues();
		}

		if (!is_array($v)) {
			$v = array();
		}
		
		parent::setValue($v);
	}
}
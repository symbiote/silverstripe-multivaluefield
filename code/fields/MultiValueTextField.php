<?php
/*

Copyright (c) 2009, SilverStripe Australia PTY LTD - www.silverstripe.com.au
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.
*/

/**
 * A text field for multivalued text entry
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MultiValueTextField extends FormField {

	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-livequery/jquery.livequery.js');
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
		return $this->createTag('span', $attributes, Convert::raw2xml($value));
	}

	public function createInput($attributes) {
		return $this->createTag('input', $attributes);
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
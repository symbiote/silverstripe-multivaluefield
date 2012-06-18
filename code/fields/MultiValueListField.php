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
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-livequery/jquery.livequery.js');
		Requirements::javascript('multivaluefield/javascript/multivaluefield.js');
		
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
			'tabindex' => $this->getTabIndex(),
			'multiple' => 'multiple',
		);

		if($this->disabled) $attrs['disabled'] = 'disabled';

		return $this->createTag('select', $attrs, $options);
	}
}
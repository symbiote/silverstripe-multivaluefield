<?php

namespace Symbiote\MultiValueField\Fields;

use Symbiote\MultiValueField\ORM\FieldType\MultiValueField;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Convert;

/**
 * A multivalued field that uses dropdown boxes for selecting the value for each
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueDropdownField extends MultiValueTextField
{
    protected $source;

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, ($title === null) ? $name : $title, $value);
        $this->source = $source;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param array $source
     * @return $this
     */
    public function setSource(array $source)
    {
        $this->source = $source;
        return $this;
    }

    public function Field($properties = [])
    {
        Requirements::javascript('symbiote/silverstripe-multivaluefield: client/dist/js/multivaluefield.js');
        Requirements::css('symbiote/silverstripe-multivaluefield: client/dist/styles/multivaluefield.css');

        $name   = $this->name.'[]';
        $fields = [];


        if ($this->value) {
            foreach ($this->value as $i => $v) {
                if ($this->readonly) {
                    $fieldAttr = [
                        'class' => 'mventryfield  mvdropdownReadonly '.($this->extraClass() ? $this->extraClass() : ''),
                        'id' => $this->id().MultiValueTextField::KEY_SEP.$i,
                        'name' => $name,
                        'tabindex' => $this->getAttribute('tabindex')
                    ];
                    $fields[]  = HTML::createTag('span', $fieldAttr, Convert::raw2xml($v));
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

        return '<ul id="'.$this->id().'" class="multivaluefieldlist '.$this->extraClass().'"><li>'.implode(
            '</li><li>',
            $fields
        ).'</li></ul>';
    }

    public function Type()
    {
        return 'dropdown multivaluedropdown';
    }

    protected function createSelectList($number, $name, $values, $selected = '')
    {
        $options = HTML::createTag(
            'option',
            [
                'selected' => $selected == '' ? 'selected' : '',
                'value' => ''
                ],
            ''
        );

        foreach ($values as $index => $title) {
            $attrs = ['value' => $index];
            if ($index == $selected) {
                $attrs['selected'] = 'selected';
            }
            $options .= HTML::createTag('option', $attrs, Convert::raw2xml($title));
        }

        $attrs = [
            'class' => 'mventryfield mvdropdown '.($this->extraClass() ? $this->extraClass() : ''),
            'id' => $this->id().MultiValueTextField::KEY_SEP.$number,
            'name' => $name,
            'tabindex' => $this->getAttribute('tabindex')
        ];

        if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        return HTML::createTag('select', $attrs, $options);
    }
}

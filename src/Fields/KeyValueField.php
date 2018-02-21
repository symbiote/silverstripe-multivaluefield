<?php

namespace Symbiote\MultiValueField\Fields;

use Symbiote\MultiValueField\ORM\FieldType\MultiValueField;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Convert;

/**
 * A field that lets you specify both a key AND a value for each row entry
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class KeyValueField extends MultiValueTextField
{
    protected $sourceKeys;
    protected $sourceValues;

    public function __construct($name, $title = null, $sourceKeys = [], $sourceValues = [], $value = null)
    {
        parent::__construct($name, ($title === null) ? $name : $title, $value);
        $this->sourceKeys   = $sourceKeys;
        $this->sourceValues = $sourceValues;
    }

    public function Field($properties = [])
    {
        if (Controller::curr() instanceof ContentController) {
            Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
        }
        Requirements::javascript('symbiote/silverstripe-multivaluefield: client/javascript/multivaluefield.js');
        Requirements::css('symbiote/silverstripe-multivaluefield: client/css/multivaluefield.css');

        $nameKey = $this->name.'[key][]';
        $nameVal = $this->name.'[val][]';
        $fields  = [];

        if ($this->value) {
            foreach ($this->value as $i => $v) {
                if ($this->readonly) {
                    $fieldAttr = [
                        'class' => 'mventryfield  mvkeyvalReadonly '.($this->extraClass() ? $this->extraClass() : ''),
                        'id' => $this->id().MultiValueTextField::KEY_SEP.$i,
                        'name' => $nameKey,
                        'tabindex' => $this->getAttribute('tabindex')
                    ];

                    $keyField        = HTML::createTag('span', $fieldAttr, Convert::raw2xml($i));
                    $fieldAttr['id'] = $this->id().MultiValueTextField::KEY_SEP.$v;
                    $valField        = HTML::createTag('span', $fieldAttr, Convert::raw2xml($v));
                    $fields[]        = $keyField.$valField;
                } else {
                    $keyField = $this->createSelectList($i, $nameKey, $this->sourceKeys, $i);
                    $valField = $this->createSelectList($i, $nameVal, $this->sourceValues, $v);
                    $fields[] = $keyField.' '.$valField;
                }
            }
        } else {
            $i = -1;
        }

        if (!$this->readonly) {
            $keyField = $this->createSelectList('new', $nameKey, $this->sourceKeys);
            $valField = $this->createSelectList('new', $nameVal, $this->sourceValues);
            $fields[] = $keyField.' '.$valField;
//          $fields[] = $this->createSelectList('new', $name, $this->source);
        }

        return '<ul id="'.$this->id().'" class="multivaluefieldlist mvkeyvallist '.$this->extraClass().'"><li>'.implode('</li><li>',
                $fields).'</li></ul>';
    }

    protected function createSelectList($number, $name, $values, $selected = '')
    {
        $options = HTML::createTag(
                'option',
                [
                'selected' => $selected == '' ? 'selected' : '',
                'value' => ''
                ], ''
        );

        foreach ($values as $index => $title) {
            $attrs = ['value' => $index];
            if ($index == $selected) {
                $attrs['selected'] = 'selected';
            }
            $options .= HTML::createTag('option', $attrs, Convert::raw2xml($title));
        }

        if (count($values)) {
            $attrs = [
                'class' => 'text mventryfield mvdropdown '.($this->extraClass() ? $this->extraClass() : ''),
                'id' => $this->id().MultiValueTextField::KEY_SEP.$number,
                'name' => $name,
                'tabindex' => $this->getAttribute('tabindex')
            ];

            if ($this->disabled) $attrs['disabled'] = 'disabled';

            return HTML::createTag('select', $attrs, $options);
        } else {
            $attrs = [
                'class' => 'text mventryfield mvtextfield '.($this->extraClass() ? $this->extraClass() : ''),
                'id' => $this->id().MultiValueTextField::KEY_SEP.$number,
                'value' => $selected,
                'name' => $name,
                'tabindex' => $this->getAttribute('tabindex'),
                'type' => 'text',
            ];

            if ($this->disabled) $attrs['disabled'] = 'disabled';

            return HTML::createTag('input', $attrs);
        }
    }

    public function setValue($v, $data = NULL)
    {
        if (is_array($v)) {
            // we've been set directly via the post - lets convert things to an appropriate key -> value
            // structure
            if (isset($v['key'])) {
                $newVal = [];

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
            $v = [];
        }

        return parent::setValue($v);
    }
}

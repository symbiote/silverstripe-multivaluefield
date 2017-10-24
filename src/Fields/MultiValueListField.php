<?php

namespace Symbiote\MultiValueField\Fields;

use Symbiote\MultiValueField\ORM\FieldType\MultiValueField;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Convert;

/**
 * A multivalued field that uses a multi choice select box for selecting the value
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueListField extends MultiValueTextField
{
    protected $source;

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, ($title === null) ? $name : $title, $value);
        $this->source = $source;
    }

    public function Field($properties = [])
    {
        if (Controller::curr() instanceof ContentController) {
            Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
        }
        Requirements::javascript('symbiote/silverstripe-multivaluefield: javascript/multivaluefield.js');
        Requirements::css('symbiote/silverstripe-multivaluefield: css/multivaluefield.css');

        $name = $this->name.'[]';

        $options = '';
        if (!$this->value) {
            $this->value = [];
        }

        foreach ($this->source as $index => $title) {
            $attrs = ['value' => $index];
            if (in_array($index, $this->value)) {
                $attrs['selected'] = 'selected';
            }
            $options .= HTML::createTag('option', $attrs, Convert::raw2xml($title));
        }

        $attrs = [
            'class' => 'mventryfield mvlistbox '.($this->extraClass() ? $this->extraClass() : ''),
            'id' => $this->id(),
            'name' => $name,
            'tabindex' => $this->getAttribute('tabindex'),
            'multiple' => 'multiple',
        ];

        if ($this->disabled) $attrs['disabled'] = 'disabled';

        return HTML::createTag('select', $attrs, $options);
    }
}
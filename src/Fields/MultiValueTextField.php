<?php

namespace Symbiote\MultiValueField\Fields;

use Symbiote\MultiValueField\ORM\FieldType\MultiValueField;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\View\HTML;
use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Convert;

/**
 * A text field for multivalued text entry
 *
 * @author Marcus Nyeholt <marcus@symbiote.com.au>
 */
class MultiValueTextField extends FormField
{
    const KEY_SEP = '__';

    protected $tag = 'input';

    /**
     * Determines whether jQuery should be added to the frontend via a CDN.
     * Set this to false if you already output your own jQuery.
     */
    private static bool $output_jquery_on_frontend = true;

    public function Field($properties = [])
    {
        if (Controller::has_curr()
            && (Controller::curr() instanceof ContentController)
            && self::config()->get('output_jquery_on_frontend')
        ) {
            Requirements::javascript('https://code.jquery.com/jquery-3.6.3.min.js');
        }
        Requirements::javascript('symbiote/silverstripe-multivaluefield: client/javascript/multivaluefield.js');
        Requirements::css('symbiote/silverstripe-multivaluefield: client/css/multivaluefield.css');

        $name   = $this->name . '[]';
        $fields = [];

        $attributes = [
            'type' => 'text',
            'class' => 'text mvtextfield mventryfield ' . ($this->extraClass() ? $this->extraClass() : ''),
            // 'id' => $this->id(),
            'name' => $name,
            // 'value' => $this->Value(),
        ];

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        $fieldAttr = $attributes;
        if ($this->value) {
            foreach ($this->value as $i => $v) {
                $fieldAttr['id']    = $this->id() . MultiValueTextField::KEY_SEP . $i;
                $fieldAttr['value'] = $v;
                if ($this->readonly) {
                    unset($fieldAttr['value']);
                    $fields[] = $this->createReadonlyInput($fieldAttr, $v);
                } else {
                    $fields[] = $this->createInput($fieldAttr, $v);
                }
            }
        }

        // add an empty row
        if (!$this->readonly) {
            // assume next pos equals to the number of existing fields which gives index+1 in a zero-indexed list
            $attributes['id'] = $this->id() . MultiValueTextField::KEY_SEP . count($fields ?? []);
            $fields[] = $this->createInput($attributes);
        }

        if (count($fields ?? [])) {
            return '<ul id="' . $this->id() . '" class="multivaluefieldlist ' . $this->extraClass() . '"><li>'
            . implode('</li><li>', $fields) . '</li></ul>';
        } else {
            return '<div id="' . $this->id() . '" class="multivaluefieldlist ' . $this->extraClass() . '"></div>';
        }
    }

    public function createReadonlyInput($attributes, $value)
    {
        return HTML::createTag('span', $attributes, Convert::raw2xml($value));
    }

    public function createInput($attributes, $value = null)
    {
        $attributes['value'] = $value;
        return HTML::createTag($this->tag, $attributes);
    }

    public function performReadonlyTransformation()
    {
        $new = clone $this;
        $new->setReadonly(true);
        return $new;
    }

    public function setValue($v, $data = null)
    {
        if (is_array($v)) {
            // we've been set directly via the post - lets prune any empty values
            foreach ($v as $key => $val) {
                if (!strlen($val ?? '')) {
                    unset($v[$key]);
                }
            }
        }
        if ($v instanceof MultiValueField) {
            $v = $v->getValues();
        }

        if (!is_array($v)) {
            $v = [];
        }

        parent::setValue($v);
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }
}

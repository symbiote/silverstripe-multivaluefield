<?php
namespace SilverStripeAustralia\MultiValueField\Tests;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

/**
 * @ignore
 */
class MultiValueFieldTest_DataObject extends DataObject implements TestOnly {

    private static $db = array(
        'MVField' => 'MultiValueField'
    );

    private static $table_name = 'MultiValueFieldTest_DataObject';

}

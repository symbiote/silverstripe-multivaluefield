<?php
namespace Symbiote\MultiValueField\Tests;

/**
 * @ignore
 */
class MultiValueFieldTest_DataObject extends \SilverStripe\ORM\DataObject implements \SilverStripe\Dev\TestOnly {

	private static $db = array(
		'MVField' => 'MultiValueField'
	);


    public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false)
    {
        parent::write($showDebug, $forceInsert, $forceWrite, $writeComponents);
    }
}

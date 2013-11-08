# SilverStripe MultiValueField module

[![Build Status](https://secure.travis-ci.org/silverstripe-australia/silverstripe-multivaluefield.png)](http://travis-ci.org/silverstripe-australia/silverstripe-multivaluefield)

Note: The SilverStripe 2.4 compatible version of the module is still available
in the ss24 branch

A database field type that allows the storage of multiple discrete values in
a single database field. This also provides form fields for entering multiple 
values in a simple manner

* MultiValueTextField - displays a text field. When data is entered, another
  text field is displayed directly beneath. Subsequent data entry triggers
  more text fields to appear
* MultiValueDropdownField - displays a dropdown field. When a value is selected
  another dropdown field is displayed. 

Within templates, the field can be iterated over as per a data object set. 
The property $Value is available as a Varchar type, and other typical 
properties such as $FirstLast etc are inherited from ViewableData.

Data is stored in the database in a serialized PHP format. While this is not
ideal for searching purposes, some external indexing engines (eg the Solr 
module) are aware of the field type and will index accordingly. 

## Version info

Version marked as 2.0.x are compatible with SilverStripe 3.1, with 2.0.1 compatible with 3.1.1.

* [SilverStripe 3.0 compatible version](https://github.com/silverstripe-australia/silverstripe-multivaluefield/tree/1.0)
* [SilverStripe 2.4 compatible version](https://github.com/silverstripe-australia/silverstripe-multivaluefield/tree/ss24)


## Basic Usage

As with all DB fields

	public static $db = array(
		'Keywords' 	=> 'MultiValueField',
	);



## Maintainer Contacts

* Marcus Nyeholt <marcus@silverstripe.com.au>

## Requirements

* SilverStripe 3.1

## Contributing

### Translations

Translations of the natural language strings are managed through a third party translation interface, transifex.com. Newly added strings will be periodically uploaded there for translation, and any new translations will be merged back to the project source code.

Please use [https://www.transifex.com/projects/p/silverstripe-multivaluefield](https://www.transifex.com/projects/p/silverstripe-multivaluefield) to contribute translations, rather than sending pull requests with YAML files.

## License

This module is licensed under the BSD license at http://silverstripe.org/BSD-license

## Project Links
* [GitHub Project Page](https://github.com/nyeholt/silverstripe-multivaluefield)
* [Issue Tracker](https://github.com/nyeholt/silverstripe-multivaluefield/issues)


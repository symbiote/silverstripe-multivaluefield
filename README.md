# SilverStripe MultiValueField module

NOTE: There was a bit of a screwup when switching to ss3; this has meant
that the ss24 branch actually contained ss3.0 code in it! This has
been reverted, but if you've previously checked out the ss24 branch, you
probably want to trash it and re-pull 

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

## Basic Usage

As with all DB fields

	public static $db = array(
		'Keywords' 	=> 'MultiValueField',
	);



## Maintainer Contacts

* Marcus Nyeholt <marcus@silverstripe.com.au>

## Requirements

* SilverStripe 2.4+

## License

This module is licensed under the BSD license at http://silverstripe.org/BSD-license

## Project Links
* [GitHub Project Page](https://github.com/nyeholt/silverstripe-multivaluefield)
* [Issue Tracker](https://github.com/nyeholt/silverstripe-multivaluefield/issues)


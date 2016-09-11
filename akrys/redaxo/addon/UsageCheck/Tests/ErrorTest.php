<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-30
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests;

/**
 * Description of ErrorTest
 *
 * @author akrys
 */
class ErrorTest
	extends \PHPUnit_Framework_TestCase
{

	/**
	 * originalzustand wieder herstellen.
	 */
	static function tearDownAfterClass()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Error', 'errors');
		$property->setAccessible(true);
		$property->setValue($errors, array());
	}

	/**
	 * Fehlerklassen-Konstruktore testen
	 */
	public function testConstruct()
	{
		$error = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\Error', get_class($error));
	}

	/**
	 * Add Funkltion testen.
	 */
	public function testAdd()
	{
		$error = \akrys\redaxo\addon\UsageCheck\Error::getInstance();

		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Error', 'errors');
		$property->setAccessible(true);
		$this->assertEquals(0, count($property->getValue($error)));

		$error->add('Test1');
		$this->assertEquals(1, count($property->getValue($error)));

		$error->add('Test2');
		$this->assertEquals(2, count($property->getValue($error)));

		$error->add('Test3');
		$this->assertEquals(3, count($property->getValue($error)));
	}

	/**
	 * Klonen testen
	 */
	public function testClone()
	{
		$this->setExpectedException('\\akrys\\redaxo\\addon\\UsageCheck\\Exception\\CloneException');

		$error = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		$error2 = clone $error;
	}

	// <editor-fold defaultstate="collapsed" desc="Iterator Tests">
	/**
	 * Iterator testen
	 */
	public function testIterator()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();

		foreach ($errors as $error) {
			$this->assertEquals('Test1', $error);

			$this->assertEquals(0, $errors->key());
			break;
		}
	}

	/**
	 * Iteratortest Funktion current()
	 */
	public function testIteratorCurrent()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		reset($errors);
		$this->assertEquals('Test1', $errors->current());
	}

	/**
	 * Iteratortest Funktion key() und next()
	 */
	public function testKeyNext()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		reset($errors);
		$this->assertEquals(0, $errors->key());

		$errors->next();
		$this->assertEquals(1, $errors->key());
	}

	/**
	 * Iteratortest Funktion rewind()
	 */
	public function testRewind()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		reset($errors);

		$errors->next();
		$errors->rewind();
		$this->assertEquals(0, $errors->key());
	}

	/**
	 * Iteratortest Funktion valid()
	 */
	public function testValid()
	{
		$errors = \akrys\redaxo\addon\UsageCheck\Error::getInstance();
		reset($errors);

		$this->assertEquals('Test1', $errors->current());

		$errors->next();
		$this->assertTrue($errors->valid());
		$this->assertEquals('Test2', $errors->current());

		$errors->next();
		$this->assertTrue($errors->valid());
		$this->assertEquals('Test3', $errors->current());

		$errors->next();
		$this->assertFalse($errors->valid());
		$this->assertFalse($errors->current());
	}
	// <-editor-fold>
}

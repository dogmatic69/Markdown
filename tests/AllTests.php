<?php
class AllTests extends PHPUnit_Framework_TestSuite {
	/**
	 * Run all the tests
	 *
	 * @return void
	 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Markdown Tests');

		$suite->addTestFile('TextTest.php');
		$suite->addTestFile('FilterTest.php');

		return $suite;
	}

}

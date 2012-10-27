<?php
/**
 * Copyright (C) 2011, Maxim S. Tsepkov
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once __DIR__ . '/../Markdown/MarkdownText.php';

class MarkdownFilterCustomTest extends MarkdownFilter {
	public function filter($text) {
		return str_replace('foo', 'bar', $text);
	}
}

class FilterTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException MarkdownFilterInvalidException
	 *
	 * @dataProvider invalidFilters
     */
    public function testFactoryNonAlnum($data) {
        MarkdownFilter::factory($data);
    }

	/**
	 * invalid filters data provider
	 *
	 * @return array
	 */
	public function invalidFilters() {
		return array(
			array('/etc/passwd'),
			array(123),
			array('asd!@#')
		);
	}

    /**
     * @expectedException MarkdownFilterNotFoundException
     */
    public function testFactoryNonExistent() {
        MarkdownFilter::factory('suchfilterdoesntexists');
    }

	/**
	 * Test the classes are correctly loaded
	 */
    public function testFactory() {
        $this->assertInstanceOf('MarkdownFilter', MarkdownFilter::factory('Hr'));
    }

	/**
	 * Check default filters are not empty
	 */
    public function testGetDefaultFiltersNonEmpty() {
        $this->assertNotEmpty(MarkdownFilter::getDefaultFilters());
    }

    /**
     * @depends testGetDefaultFiltersNonEmpty
     */
    public function testSetDefaultFilters() {
        $filters = array('Linebreak', 'Hr');
        MarkdownFilter::setDefaultFilters($filters);
        $this->assertEquals(MarkdownFilter::getDefaultFilters(), $filters);
    }

    /**
     * @expectedException MarkdownFilterInvalidException
	 *
	 * @dataProvider invalidFilterDataProvider
     */
    public function testRunWithInvalidFiltersParameter($data) {
        MarkdownFilter::run('', array(1, false, true));
    }

	/**
	 * Data provider for various filter fails
	 *
	 * @return array
	 */
	public function invalidFilterDataProvider() {
		return array(
			'null' => array(null),
			'bool' => array(true),
			'char' => array('filter!@#')

		);
	}

    /**
	 * default filters
	 *
	 * @dataProvider invalidFilterDataProvider
     */
    public function testRunWithDefaultFilters($data) {
        MarkdownFilter::run('');
    }

	/**
	 * test outdenting of code
	 *
	 * @dataProvider outdentDataProvider
	 */
	public function testOutdent($data, $expected) {
		$result = MarkdownFilter::run($data, array('Code'));

		$this->assertEquals($expected, $result);
	}

	/**
	 * outdent data provider
	 *
	 * @return array
	 */
	public function outdentDataProvider() {
		return array(
			'tabs' => array(
				"\n\t\tSome text\n",
				"\n\n<pre><code>\tSome text\n</code></pre>\n\n",
			),
			'spaces' => array(
				"\n        Some text\n",
				"\n\n<pre><code>    Some text\n</code></pre>\n\n",
			),
			'mixed' => array(
				"\n    \tSome text\n",
				"\n\n<pre><code>\tSome text\n</code></pre>\n\n",
			)
		);
	}

	/**
	 * test custom filters
	 */
	public function testCustomFilters() {
		$expected = 'some text bar';
		$result = MarkdownFilter::run('some text foo', array('CustomTest'));
		$this->assertEquals($expected, $result);
	}

}

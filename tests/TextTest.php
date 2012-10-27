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

class TextTest extends PHPUnit_Framework_TestCase {
	/**
	 * @brief markdown
	 *
	 * @var array
	 */
    protected static $_md   = array();

	/**
	 * html
	 * @var array
	 */
    protected static $_html = array();

	/**
	 * @brief before
	 */
    public static function setUpBeforeClass() {
        $mds = glob(__DIR__ . '/data/*.md');
        foreach($mds as $filename) {
            $key = basename($filename, '.md');
            self::$_md[$key] = file_get_contents($filename);
        }

        $htmls = glob(__DIR__ . '/data/*.html');
        foreach($htmls as $filename) {
            $key = basename($filename, '.html');
            self::$_html[$key] = file_get_contents($filename);
        }
    }

	/**
	 * test creating a new class
	 */
    public function testConstruct() {
        $text = new MarkdownText(self::$_md['syntax']);
        $this->assertEquals(self::$_md['syntax'], $text->getMarkdown());
    }

	/**
	 * test set markdown
	 */
    public function testSetGetMarkdown() {
        $text = new MarkdownText();
        $text->setMarkdown(self::$_md['basics']);
        $this->assertEquals(self::$_md['basics'], $text->getMarkdown());
    }

	/**
	 * test get html
	 */
    public function testGetHtml() {
        $text = new MarkdownText();
        $text->setMarkdown("\nSome text\n");
        $this->assertEquals("<p>Some text</p>\n", $text->getHtml());
    }

	/**
	 * test filter basics
	 *
	 * @dataProvider filterTextDataProvider
	 */
	public function testFilterText($data, $expected) {
		$data = sprintf($data, 'Some text');

        $MarkdownText = new MarkdownText();
        $MarkdownText->setMarkdown($data);

		$result = $MarkdownText->getHtml();
        $this->assertTags($result, $expected);
	}

	/**
	 * filter text data provider
	 *
	 * @return array
	 */
	public function filterTextDataProvider() {
		return array(
			'blockquote' => array(
				'> %s',
				array(
					array('blockquote' => array()),
						'Some text',
					'/blockquote'
				)
			),
			'blockquote_double_space' => array(
				'>> %s',
				array(
					array('blockquote' => array()),
						array('blockquote' => array()),
							'Some text',
						'/blockquote',
					'/blockquote'
				)
			),
			'blockquote_double_space' => array(
				'> > %s',
				array(
					array('blockquote' => array()),
						array('blockquote' => array()),
							'Some text',
						'/blockquote',
					'/blockquote'
				)
			),
			'blockquote_other_markdown' => array(
				'> **%s**',
				array(
					array('blockquote' => array()),
						array('strong' => array()),
							'Some text',
						'/strong',
					'/blockquote'
				)
			),/* removing the last > by '> still'
			'blockquote_quote_skips_>' => array(
				"> Have \"> \n%s > another\n> still\" here",
				array(
					array('blockquote' => array()),
						"Have \"> \nSome text > another\n > still\" here",
					'/blockquote'
				)
			), */
			'blockquote_li' => array(
				'> - %s',
				array(
					array('blockquote' => array()),
						array('ul' => array()),
							array('li' => array()),
								'Some text',
							'/li',
						'/ul',
					'/blockquote'
				)
			),
			'emphasis _' => array(
				'_%s_',
				array(
					array('p' => array()),
						array('em' => array()),
							'Some text',
						'/em',
					'/p'
				)
			),
			'emphasis *' => array(
				'*%s*',
				array(
					array('p' => array()),
						array('em' => array()),
							'Some text',
						'/em',
					'/p'
				)
			),
			'emphasis _ mid word' => array(
				'%s c_oo_l',
				array(
					array('p' => array()),
						'Some text c',
						array('em' => array()),
							'oo',
						'/em',
						'l',
					'/p'
				)
			),
			'emphasis * mid word' => array(
				'%s c*oo*l',
				array(
					array('p' => array()),
						'Some text c',
						array('em' => array()),
							'oo',
						'/em',
						'l',
					'/p'
				)
			),
			'emphasis __' => array(
				'__%s__',
				array(
					array('p' => array()),
						array('strong' => array()),
							'Some text',
						'/strong',
					'/p'
				)
			),
			'emphasis **' => array(
				'**%s**',
				array(
					array('p' => array()),
						array('strong' => array()),
							'Some text',
						'/strong',
					'/p'
				)
			),/* does not render the em
			'emphasis with escape' => array(
				'_Some\_text',
				array(
					array('p' => array()),
						array('em' => array()),
							'Some_text',
						'/em',
					'/p'
				)
			),*/
			'entities' => array(
				'%s & more',
				array(
					array('p' => array()),
						'Some text &amp; more',
					'/p'
				)
			),
			'header_no_space' => array(
				"#%s\n",
				array(
					array('h1' => array()),
						'Some text',
					'/h1'
				)
			),
			'header_space' => array(
				"## %s\n",
				array(
					array('h2' => array()),
						'Some text',
					'/h2'
				)
			),
			'header_em' => array(
				"###### _%s_\n",
				array(
					array('h6' => array()),
						array('em' => array()),
							'Some text',
						'/em',
					'/h6'
				)
			),
			'header_too_many' => array(
				"####### %s\n",
				array(
					array('h6' => array()),
						'# Some text',
					'/h6'
				)
			),
			'header_underline_h1' => array(
				"%s\n=\n",
				array(
					array('h1' => array()),
						'Some text',
					'/h1'
				)
			),
			'header_underline_h2' => array(
				"%s\n-\n",
				array(
					array('h2' => array()),
						'Some text',
					'/h2'
				)
			),
			'header_underline_h2_more' => array(
				"%s\n------------------------\n",
				array(
					array('h2' => array()),
						'Some text',
					'/h2'
				)
			),
			'header_close_space' => array(
				"## %s ##\n",
				array(
					array('h2' => array()),
						'Some text',
					'/h2'
				)
			),
			'header_close_no_space' => array(
				"##%s##\n",
				array(
					array('h2' => array()),
						'Some text',
					'/h2'
				)
			),
			'hr -' => array(
				"%s\n\n---",
				array(
					array('p' => array()),
						'Some text',
					'/p',
					array('p' => array()),
						array('hr' => array()),
					'/p',
				)
			),
			'hr - space' => array(
				"%s\n\n- - -",
				array(
					array('p' => array()),
						'Some text',
					'/p',
					array('p' => array()),
						array('hr' => array()),
					'/p',
				)
			),/* does not make hr
			'hr *' => array(
				"%s\n\n***\n",
				array(
					array('p' => array()),
						'Some text',
					'/p',
					array('p' => array()),
						array('hr' => array()),
					'/p',
				)
			),*/
			'hr * space' => array(
				"%s\n\n* * *\n",
				array(
					array('p' => array()),
						'Some text',
					'/p',
					array('p' => array()),
						array('hr' => array()),
					'/p',
				)
			),/*
			'hr _' => array(
				"%s\n\n___\n",
				array(
					array('p' => array()),
						'Some text',
					'/p',
					array('p' => array()),
						array('hr' => array()),
					'/p',
				)
			),*/
			'img' => array(
				"![](/some/image.png)",
				array(
					array('p' => array()),
						array('img' => array(
							'src' => '/some/image.png',
							'alt' => ''
						)),
					'/p'
				)
			),
			'img_alt' => array(
				"![%s](/some/image.png)",
				array(
					array('p' => array()),
						array('img' => array(
							'src' => '/some/image.png',
							'alt' => 'Some text'
						)),
					'/p'
				)
			), /* makes em in alt tag.
			'img_alt no_em' => array(
				"![_%s_](/some/image.png)",
				array(
					array('p' => array()),
						array('img' => array(
							'src' => '/some/image.png',
							'alt' => '_Some text_'
						)),
					'/p'
				)
			),*/
			'img_title' => array(
				"![](/some/image.png \"%s\")",
				array(
					array('p' => array()),
						array('img' => array(
							'src' => '/some/image.png',
							'alt' => '',
							'title' => 'Some text'
						)),
					'/p'
				)
			),
			'img_title_alt' => array(
				"![alt text](/some/image.png \"%s\")",
				array(
					array('p' => array()),
						array('img' => array(
							'src' => '/some/image.png',
							'alt' => 'alt text',
							'title' => 'Some text'
						)),
					'/p'
				)
			),
			'line break' => array(
				"%s\nmore text  \nagain\n",
				array(
					array('p' => array()),
						"Some text\nmore text",
						array('br' => array()),
						'again',
					'/p'
				)
			), /*
			'link_simple' => array(
				'see <http://example.com>\n',
				array(
					array('p' => array()),
						'see ',
						array('a' => array(
							'href' => 'http://example.com'
						)),
							'http://example.com',
						'/a',
					'/p'
				)
			),
			'link_mail' => array(
				'email <foo@bar.com>',
				array(
					array('a' => array(
						'href' => 'mailto:foo@bar.com'
					)),
						'foo@bar.com',
					'/a'
				)
			),*/
			'link_relative' => array(
				'[%s](/some/url)',
				array(
					array('p' => array()),
						array('a' => array(
							'href' => '/some/url'
						)),
							'Some text',
						'/a',
					'/p'
				)
			),
			'link_title' => array(
				'[%s](/some/url "title text")',
				array(
					array('p' => array()),
						array('a' => array(
							'href' => '/some/url',
							'title' => 'title text'
						)),
							'Some text',
						'/a',
					'/p'
				)
			), /*
			'link_ref' => array(
				"see [%s][1] here\n\nMore text\n\n[1]: \"/some/url\"\n\n",
				array(

				)
			), */
			'list -' => array(
				"- %s\n- more",
				array(
					array('ul' => array()),
						array('li' => array()),
							'Some text',
						'/li',
						array('li' => array()),
							'more',
						'/li',
					'/ul'
				)
			),
			'list *' => array(
				"- %s\n- more",
				array(
					array('ul' => array()),
						array('li' => array()),
							'Some text',
						'/li',
						array('li' => array()),
							'more',
						'/li',
					'/ul'
				)
			),
			'list 1' => array(
				"1. %s\n1. more",
				array(
					array('ol' => array()),
						array('li' => array()),
							'Some text',
						'/li',
						array('li' => array()),
							'more',
						'/li',
					'/ol'
				)
			),/* does not render more html
			'list_with_block_quote' => array(
				"- %s\n   > more text",
				array(
					array('ul' => array()),
						array('li' => array()),
							'Some text',
						'/li',
					'/ul'
				)
			),/*
			'list p' => array(
				"- %s\n\tthat is\n    long",
				array(
					array('ul' => array()),
						array('li' => array()),
							'Some text',
						'/li',
					'/ul'
				)
			), */
			'new_line' => array(
				"%s\r\nthat\ris broken",
				array(
					array('p' => array()),
						"Some text\nthat\nis broken",
					'/p'
				)
			),
			'p' => array(
				'%s',
				array(
					array('p' => array()),
						'Some text',
					'/p'
				)
			),
			'unescape _ *' => array(
				'\\*%s\\_',
				array(
					array('p' => array()),
						'*Some text_',
					'/p'
				)
			),
			'unescape _' => array(
				'\\+%s\\-',
				array(
					array('p' => array()),
						'+Some text-',
					'/p'
				)
			),
			'<' => array(
				'%s < other text',
				array(
					array('p' => array()),
						'Some text &lt; other text',
					'/p'
				)
			),
			'&amp_not_escaped' => array(
				'%s &amp; more',
				array(
					array('p' => array()),
						'Some text &amp; more',
					'/p'
				)
			),
			'&_escaped' => array(
				'%s & more',
				array(
					array('p' => array()),
						'Some text &amp; more',
					'/p'
				)
			),
			'inline_html_simple' => array(
				'<p class="foobar">%s</p>',
				array(
					array('p' => array(
						'class' => 'foobar'
					)),
						'Some text',
					'/p'
				)
			),
			'inline_html_tags' => array(
				'This is a p with <b>%s</b> that is bold',
				array(
					array('p' => array()),
						'This is a p with ',
						array('b' => array()),
							'Some text',
						'/b',
						' that is bold',
					'/p'
				)
			),
			'code' => array(
				"\t%s\n",
				array(
					array('pre' => array()),
						array('code' => array()),
							'Some text',
						//'/code',
					//'/pre',
				)
			),
			'code_html' => array(
				"\t<div>%s</div>\n",
				array(
					array('pre' => array()),
						array('code' => array()),
							'&lt;div&gt;Some text&lt;/div&gt;',
						//'/code',
					//'/pre'
				)
			), /** escaping indents issue #3
			'code_html_with_indents' => array(
				"\t<div>\n\t\t<b>%s</b>\n\t</div>\n",
				array(
					array('pre' => array()),
						array('code' => array()),
							'&lt;div&gt;' .
								'&lt;b&gt;Some text&lt;b&gt;'.
							'&lt;/div&gt;',
						//'/code',
					//'/pre'
				)
			), */
			'code_inline' => array(
				'inline `%s` stuff',
				array(
					array('p' => array()),
						'inline ',
						array('code' => array()),
							'Some text',
						'/code',
						' stuff',
					'/p'
				)
			),
			'code literal backtick' => array(
				'%s `` ` ``',
				array(
					array('p' => array()),
						'Some text ',
						array('code' => array()),
							'`',
						'/code',
					'/p'
				)
			),
			'code with backtick' => array(
				'%s `` `some code` ``',
				array(
					array('p' => array()),
						'Some text ',
						array('code' => array()),
							'`some code`',
						'/code',
					'/p'
				)
			),
			'code escape' => array(
				'`&#8212;` is the decimal-encoded equivalent of `&mdash;`.',
				array(
					array('p' => array()),
						array('code' => array()),
							'&amp;#8212;',
						'/code',
						' is the decimal-encoded equivalent of ',
						array('code' => array()),
							'&amp;mdash;',
						'/code',
						'.',
					'/p'
				)
			)
		);
	}

	/**
	 * Takes an array $expected and generates a regex from it to match the provided $string.
	 * Samples for $expected:
	 *
	 * Checks for an input tag with a name attribute (contains any non-empty value) and an id
	 * attribute that contains 'my-input':
	 * 	array('input' => array('name', 'id' => 'my-input'))
	 *
	 * Checks for two p elements with some text in them:
	 * 	array(
	 * 		array('p' => true),
	 * 		'textA',
	 * 		'/p',
	 * 		array('p' => true),
	 * 		'textB',
	 * 		'/p'
	 *	)
	 *
	 * You can also specify a pattern expression as part of the attribute values, or the tag
	 * being defined, if you prepend the value with preg: and enclose it with slashes, like so:
	 *	array(
	 *  	array('input' => array('name', 'id' => 'preg:/FieldName\d+/')),
	 *  	'preg:/My\s+field/'
	 *	)
	 *
	 * Important: This function is very forgiving about whitespace and also accepts any
	 * permutation of attribute order. It will also allow whitespace between specified tags.
	 *
	 * Taken from the CakePHP framework test suite.
	 *
	 * @link https://github.com/cakephp/cakephp/blob/master/lib/Cake/TestSuite/CakeTestCase.php
	 * @link http://cakephp.org
	 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 *
	 * @param string $string An HTML/XHTML/XML string
	 * @param array $expected An array, see above
	 * @param string $message SimpleTest failure output string
	 * @return boolean
	 */
	public function assertTags($string, $expected, $fullDebug = false) {
		$regex = array();
		$normalized = array();
		foreach ((array)$expected as $key => $val) {
			if (!is_numeric($key)) {
				$normalized[] = array($key => $val);
			} else {
				$normalized[] = $val;
			}
		}
		$i = 0;
		foreach ($normalized as $tags) {
			if (!is_array($tags)) {
				$tags = (string)$tags;
			}
			$i++;
			if (is_string($tags) && $tags{0} == '<') {
				$tags = array(substr($tags, 1) => array());
			} elseif (is_string($tags)) {
				$tagsTrimmed = preg_replace('/\s+/m', '', $tags);

				if (preg_match('/^\*?\//', $tags, $match) && $tagsTrimmed !== '//') {
					$prefix = array(null, null);

					if ($match[0] == '*/') {
						$prefix = array('Anything, ', '.*?');
					}
					$regex[] = array(
						sprintf('%sClose %s tag', $prefix[0], substr($tags, strlen($match[0]))),
						sprintf('%s<[\s]*\/[\s]*%s[\s]*>[\n\r]*', $prefix[1], substr($tags,  strlen($match[0]))),
						$i,
					);
					continue;
				}
				if (!empty($tags) && preg_match('/^preg\:\/(.+)\/$/i', $tags, $matches)) {
					$tags = $matches[1];
					$type = 'Regex matches';
				} else {
					$tags = preg_quote($tags, '/');
					$type = 'Text equals';
				}
				$regex[] = array(
					sprintf('%s "%s"', $type, $tags),
					$tags,
					$i,
				);
				continue;
			}
			foreach ($tags as $tag => $attributes) {
				$regex[] = array(
					sprintf('Open %s tag', $tag),
					sprintf('[\s]*<%s', preg_quote($tag, '/')),
					$i,
				);
				if ($attributes === true) {
					$attributes = array();
				}
				$attrs = array();
				$explanations = array();
				$i = 1;
				foreach ($attributes as $attr => $val) {
					if (is_numeric($attr) && preg_match('/^preg\:\/(.+)\/$/i', $val, $matches)) {
						$attrs[] = $matches[1];
						$explanations[] = sprintf('Regex "%s" matches', $matches[1]);
						continue;
					} else {
						$quotes = '["\']';
						if (is_numeric($attr)) {
							$attr = $val;
							$val = '.+?';
							$explanations[] = sprintf('Attribute "%s" present', $attr);
						} elseif (!empty($val) && preg_match('/^preg\:\/(.+)\/$/i', $val, $matches)) {
							$quotes = '["\']?';
							$val = $matches[1];
							$explanations[] = sprintf('Attribute "%s" matches "%s"', $attr, $val);
						} else {
							$explanations[] = sprintf('Attribute "%s" == "%s"', $attr, $val);
							$val = preg_quote($val, '/');
						}
						$attrs[] = '[\s]+' . preg_quote($attr, '/') . '=' . $quotes . $val . $quotes;
					}
					$i++;
				}
				if ($attrs) {
					$permutations = $this->_arrayPermute($attrs);

					$permutationTokens = array();
					foreach ($permutations as $permutation) {
						$permutationTokens[] = implode('', $permutation);
					}
					$regex[] = array(
						sprintf('%s', implode(', ', $explanations)),
						$permutationTokens,
						$i,
					);
				}
				$regex[] = array(
					sprintf('End %s tag', $tag),
					'[\s]*\/?[\s]*>[\n\r]*',
					$i,
				);
			}
		}
		foreach ($regex as $i => $assertation) {
			list($description, $expressions, $itemNum) = $assertation;
			$matches = false;
			foreach ((array)$expressions as $expression) {
				if (preg_match(sprintf('/^%s/s', $expression), $string, $match)) {
					$matches = true;
					$string = substr($string, strlen($match[0]));
					break;
				}
			}
			if (!$matches) {
				$this->assertTrue(false, sprintf('Item #%d / regex #%d failed: %s', $itemNum, $i, $description));
				if ($fullDebug) {
					debug($string, true);
					debug($regex, true);
				}
				return false;
			}
		}

		$this->assertTrue(true, '%s');
		return true;
	}

	/**
	 * Generates all permutation of an array $items and returns them in a new array.
	 *
	 * Taken from the CakePHP framework test suite.
	 *
	 * @link https://github.com/cakephp/cakephp/blob/master/lib/Cake/TestSuite/CakeTestCase.php
	 * @link http://cakephp.org
	 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 *
	 * @param array $items An array of items
	 * @return array
	 */
	protected function _arrayPermute($items, $perms = array()) {
		static $permuted;
		if (empty($perms)) {
			$permuted = array();
		}

		if (empty($items)) {
			$permuted[] = $perms;
		} else {
			$numItems = count($items) - 1;
			for ($i = $numItems; $i >= 0; --$i) {
				$newItems = $items;
				$newPerms = $perms;
				list($tmp) = array_splice($newItems, $i, 1);
				array_unshift($newPerms, $tmp);
				$this->_arrayPermute($newItems, $newPerms);
			}
			return $permuted;
		}
	}

}

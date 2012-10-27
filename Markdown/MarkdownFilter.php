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

/**
 * Superclass of all filters.
 *
 * Provides static methods to configure and use filtering system.
 *
 * @package Markdown
 * @subpackage Filter
 * @author Max Tsepkov <max@garygolden.me>
 * @version 1.0
 */
abstract class MarkdownFilter {
	/**
	 * Default filters
	 *
	 * @var array
	 */
	protected static $_defaultFilters = array(
		'Newline',
		'Blockquote',
		'Code',
		'Emphasis',
		'Entities',
		'HeaderAtx',
		'HeaderSetext',
		'Hr',
		'Img',
		'Linebreak',
		'Link',
		'ListBulleted',
		'ListNumbered',
		'Paragraph',
		'Unescape'
	);

	/**
	 * List of characters which copies as is after \ char.
	 *
	 * @var array
	 */
	protected static $_escapableChars = array(
		'\\', '`', '*', '_', '{', '}', '[', ']',
		'(' , ')', '#', '+', '-', '.', '!'
	);

	/**
	 * Block-level HTML tags.
	 *
	 * @var array
	 */
	protected static $_blockTags = array(
		'p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre',
		'table', 'dl', 'ol', 'ul', 'script', 'noscript', 'form', 'fieldset',
		'iframe', 'math', 'ins', 'del', 'article', 'aside', 'header', 'hgroup',
		'footer', 'nav', 'section', 'figure', 'figcaption'
	);

	/**
	 * Lookup MarkdownFilter{$filtername} class and return its instance.
	 *
	 * @param string $filtername
	 *
	 * @return Markdown_Filter
	 *
	 * @throws InvalidArgumentException
	 */
	public static function factory($filtername) {
		if (!is_string($filtername) || !ctype_alnum($filtername)) {
			throw new MarkdownFilterInvalidException(array($filtername));
		}

		$class = 'MarkdownFilter'   . $filtername;
		$file  = __DIR__ . '/Filter/' . $class . '.php';

		if (!is_readable($file)) {
			throw new MarkdownFilterNotFoundException(array($class));
		}
		require_once $file;

		if (!class_exists($class)) {
			throw new MarkdownFilterNotFoundException(array($class));
		}

		return new $class;
	}

	/**
	 * Get the default filters
	 *
	 * @return array
	 */
	public static function getDefaultFilters() {
		return self::$_defaultFilters;
	}

	/**
	 * Set default filters
	 *
	 * @param array $filters the filters to use
	 */
	public static function setDefaultFilters(array $filters) {
		self::$_defaultFilters = $filters;
	}

	/**
	 * Pass given $text through $filters chain and return result.
	 * Use default filters in no $filters given.
	 *
	 * @param string $text the markdown text
	 * @param array $filters optional list of filters to run
	 *
	 * @return string
	 */
	public static function run($text, array $filters = null){
		if ($filters === null) {
			$filters = self::getDefaultFilters();
		}

		foreach ($filters as $filter) {
			if (is_string($filter)) {
				$filter = self::factory($filter);
			}
			if (!$filter instanceof MarkdownFilter) {
				throw new MarkdownFilterInvalidException(array($fiter));
			}

			$text = $filter->filter($text);
		}

		return $text;
	}

	/**
	 * Remove one level of indentation
	 *
	 * @param string text to outdent
	 *
	 * @return string
	 */
	protected static function _outdent($text) {
		return preg_replace('/^(\t| {1,4})/m', '', $text);
	}

	abstract public function filter($text);

}

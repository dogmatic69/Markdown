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

require_once __DIR__ . '/MarkdownFilter.php';

/**
 * Represents a piece of text which can be both markdown and html.
 *
 * @package Markdown
 * @subpackage Text
 * @author Max Tsepkov <max@garygolden.me>
 * @version 1.0
 */
class MarkdownText {
    /**
     * Internal cache of the markdown
	 *
     * @var string
     */
    protected $_markdown;

    /**
     * Internal cache of the html
	 *
     * @var string
     */
    protected $_html;

    /**
     *
     * @param string $markdown
     */
    public function __construct($markdown = '') {
        $this->setMarkdown($markdown);
    }

    /**
     * Get the html
	 *
	 * If not already converted, the markdown will be converted to html
	 *
     * @return string
     */
    public function getHtml() {
        if ($this->_html === null) {
            $this->_html = MarkdownFilter::run($this->getMarkdown());
        }

        return $this->_html;
    }

    /**
     * Get the markdown
	 *
     * @return string
     */
    public function getMarkdown() {
        return $this->_markdown;
    }

    /**
     * Set the markdown
	 *
	 * If the markdown is the same as what has been set previously nothing is done
	 *
     * @param string $markdown the markdown to be converted
	 *
     * @return MarkdownText
     */
    public function setMarkdown($markdown) {
        $markdown = (string)$markdown;

        if ($markdown !== $this->_markdown) {
            $this->_markdown = $markdown;
            $this->_html     = null;
        }

        return $this;
    }

}

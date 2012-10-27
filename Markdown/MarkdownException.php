<?php
class MarkdownException extends Exception {
	/**
	 * @brief the message template
	 *
	 * @var string
	 */
	protected $_message = 'An error occured';

	/**
	 * Format the exception message with the passed params
	 *
	 * @param type $message
	 * @param type $code
	 * @param type $previous
	 */
	public function __construct($message = null, $code = null, $previous = null) {
		if(is_array($message)) {
			$message = array_filter($message);
			if(!empty($message)) {
				$message = vsprintf($this->_message, $message);
			}
		}
		if (empty($mesage) && !empty($this->_message)) {
			$mesage = $this->_message;
		}

		parent::__construct((string)$message, $code, $previous);
	}

}

/**
 * Filter not found exception
 */
class MarkdownFilterNotFoundException extends MarkdownException {
	protected $_message = 'Markdown filter "%s" not found';
}

/**
 * Invalid filter
 */
class MarkdownFilterInvalidException extends MarkdownException {
	protected $_message = 'Invalid markdown filter "%s"';
}
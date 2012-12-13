<?php

/*
 * Copyright (c) 2012, Lienhart Woitok <mail@liwo.org>
 *
 * Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby
 * granted, provided that the above copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
 * INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN
 * AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
 * PERFORMANCE OF THIS SOFTWARE.
 */

namespace JsWatch\Compiler;

/**
 * A message from the compiler
 */
class Message {

	/**
	 * @var string
	 */
	protected $type = 'Message';

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var integer
	 */
	protected $lineNumber;

	/**
	 * @var integer
	 */
	protected $characterNumber;

	/**
	 * @var string
	 */
	protected $line;

	/**
	 * @param string $message
	 * @param integer $lineNumber
	 * @param integer $characterNumber
	 * @param string $line
	 */
	public function __construct($message, $lineNumber = 0, $characterNumber = 0, $line = '') {
		$this->message = $message;
		$this->lineNumber = $lineNumber;
		$this->characterNumber = $characterNumber;
		$this->line = $line;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getCharacterNumber() {
		return $this->resolveTabsInCharacterNumber($this->characterNumber, $this->line);
	}

	/**
	 * @return string
	 */
	public function getLine() {
		return $this->convertTabsToSpaces($this->line);
	}

	/**
	 * @return int
	 */
	public function getLineNumber() {
		return $this->lineNumber;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getLineMark() {
		return str_pad('^', $this->getCharacterNumber(), ' ', STR_PAD_LEFT);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getType() . ' in line ' . $this->getLineNumber() . ', character ' . $this->getCharacterNumber() . ': ' . $this->getMessage() . PHP_EOL
				. $this->getLine() . PHP_EOL
				. $this->getLineMark();
	}

	/**
	 * @param string $string
	 * @param integer $tabWidth
	 * @return string
	 */
	protected function convertTabsToSpaces($string, $tabWidth = 4) {
		while (($pos = strpos($string, "\t")) !== FALSE) {
			$string = substr($string, 0, $pos) . str_repeat(' ', $tabWidth - $pos % $tabWidth) . substr($string, $pos + 1);
		}
		return $string;
	}

	/**
	 * @param integer $characterNumber
	 * @param string $line
	 * @param integer $tabWidth
	 * @return integer
	 */
	protected function resolveTabsInCharacterNumber($characterNumber, $line, $tabWidth = 4) {
		return strlen($this->convertTabsToSpaces(substr($line, 0, $characterNumber), $tabWidth));
	}
}

?>

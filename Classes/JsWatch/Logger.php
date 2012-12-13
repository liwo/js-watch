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

namespace JsWatch;

/**
 *
 */
class Logger {

	const LEVEL_DEBUG = LOG_DEBUG;
	const LEVEL_INFO = LOG_INFO;
	const LEVEL_OK = LOG_LOCAL0;
	const LEVEL_WARNING = LOG_WARNING;
	const LEVEL_ERROR = LOG_ERR;
	const LEVEL_CRITICAL = LOG_CRIT;

	/**
	 * @var bool
	 */
	protected $enableColors = FALSE;

	/**
	 * @var array
	 */
	protected $colorTable = array(
		self::LEVEL_DEBUG => array('', ''),
		self::LEVEL_INFO => array('', ''),
		self::LEVEL_OK => array("\033[0;32m", "\033[0m"),
		self::LEVEL_WARNING => array("\033[0;33m", "\033[0m"),
		self::LEVEL_ERROR => array("\033[0;31m", "\033[0m"),
		self::LEVEL_CRITICAL => array("\033[1;31m", "\033[0m"),
	);

	/**
	 * @var string
	 */
	protected static $lastMessageTime = '';

	/**
	 * @var Logger
	 */
	private static $instance = NULL;

	/**
	 * Singleton constructor, may not be called
	 */
	private function __construct() {
		// Disable colors for non tty consoles
		if (function_exists('posix_isatty') AND @posix_isatty(STDOUT)) {
			$this->enableColors = TRUE;
		}
	}

	/**
	 * @return Logger
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Write a message
	 *
	 * @param string $message
	 * @param int $level
	 * @param resource $file Target file descriptor, e.g. STDOUT or STDERR
	 */
	public function log($message, $level = self::LEVEL_INFO, $file = STDOUT) {
		$time = '[' . strftime('%H:%M:%S') . '] ';
		if ($time === self::$lastMessageTime) {
			$time = str_repeat(' ', strlen($time));
		} else {
			self::$lastMessageTime = $time;
		}
		foreach (explode("\n", $message) as $line) {
			fwrite($file, $time . $this->colorize($line, $level) . PHP_EOL);
		}
	}

	/**
	 * Log a debug message
	 *
	 * @param string $message
	 */
	public function debug($message) {
		$this->log($message, self::LEVEL_DEBUG);
	}

	/**
	 * Log a info message
	 *
	 * @param string $message
	 */
	public function info($message) {
		$this->log($message, self::LEVEL_INFO);
	}

	/**
	 * Log a success message
	 *
	 * @param string $message
	 */
	public function ok($message) {
		$this->log($message, self::LEVEL_OK);
	}

	/**
	 * Log a warning message
	 *
	 * @param string $message
	 */
	public function warn($message) {
		$this->log($message, self::LEVEL_WARNING);
	}

	/**
	 * Log an error message
	 *
	 * @param string $message
	 */
	public function error($message) {
		$this->log($message, self::LEVEL_ERROR);
	}

	/**
	 * Log a critical error message. This logs to STDERR.
	 *
	 * @param string $message
	 */
	public function critical($message) {
		$this->log($message, self::LEVEL_CRITICAL, STDERR);
	}

	/**
	 * Add ANSI color code escapes to a given string
	 *
	 * @param string $string
	 * @param integer $level
	 * @return string
	 */
	protected function colorize($string, $level) {
		if ($this->enableColors) {
			if (isset($this->colorTable[$level])) {
				$string = $this->colorTable[$level][0] . $string . $this->colorTable[$level][1];
			}
		}
		return $string;
	}
}

?>

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
 * Bootstraps the system
 */
class Bootstrap {

	/**
	 * Construct the class and start initializing everything
	 */
	private function __construct() {
		spl_autoload_register(array($this, 'autoLoad'));
		$this->registerSignalHandlers();
	}

	/**
	 * Run the bootstrap process. After calling this method, the system is fully initialized
	 */
	public static function run() {
		new self();
	}

	/**
	 * Stop the script
	 */
	public static function shutdown() {
		exit();
	}

	/**
	 * Auto load class.
	 *
	 * Code taken from https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md#example-implementation
	 * and adapted.
	 *
	 * @param string $className Name of the class to load
	 * @return boolean Whether the class could be loaded
	 */
	public function autoLoad($className) {
		$className = ltrim($className, '\\');
		$fileName = __DIR__ . '/../';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		if (file_exists($fileName)) {
			require $fileName;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	protected function registerSignalHandlers() {
		pcntl_signal(SIGTERM, array($this, 'shutdown'));
		pcntl_signal(SIGINT, array($this, 'shutdown'));
	}
}

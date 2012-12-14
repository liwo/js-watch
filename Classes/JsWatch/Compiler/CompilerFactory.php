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
 * Factory for compiler
 */
class CompilerFactory {

	/**
	 * @var CompilerFactory
	 */
	private static $instance;

	/**
	 * List of class names of available compilers
	 *
	 * @var array
	 */
	protected $availableCompilerClasses = array();

	/**
	 * Private constructor for singleton
	 */
	private function __construct() {
		$this->availableCompilerClasses = array(
			'JsWatch\\Compiler\\GoogleClosureCompilerApplication',
			'JsWatch\\Compiler\\GoogleClosureCompilerService',
		);
	}

	/**
	 * @return CompilerFactory
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * @return CompilerInterface
	 */
	public function getNewCompiler() {
		foreach ($this->availableCompilerClasses as $className) {
			try {
				/** @var $compiler CompilerInterface */
				$compiler = new $className();
				\JsWatch\Logger::getInstance()->debug('Using compiler ' . $className . '.');
				return $compiler;
			} catch (\JsWatch\Exception\MissingDependencyException $e) {
				\JsWatch\Logger::getInstance()->debug('Not using compiler ' . $className . '. ' . $e->getMessage());
			}
		}
		throw new \JsWatch\Exception\MissingDependencyException('Could not instantiate any compiler', 1355441071);
	}
}

?>

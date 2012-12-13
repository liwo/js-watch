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
 * Compilation result
 */
class CompilationResult {

	/**
	 * Compiled source file
	 *
	 * @var \SplFileInfo
	 */
	protected $sourceFile;

	/**
	 * Compilation target file
	 *
	 * @var \SplFileInfo
	 */
	protected $targetFile;

	/**
	 * Whether compilation was successful
	 *
	 * @var boolean
	 */
	protected $successful;

	/**
	 * @var string
	 */
	protected $compiledCode;

	/**
	 * List of errors which occurred during compilation
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * List of warnings which where emitted during compilation
	 *
	 * @var array
	 */
	protected $warnings = array();

	/**
	 * @param \SplFileInfo $sourceFile
	 * @param \SplFileInfo $targetFile
	 * @param bool $successful
	 */
	public function __construct(\SplFileInfo $sourceFile, \SplFileInfo $targetFile = NULL, $successful = FALSE) {
		$this->sourceFile = $sourceFile;
		$this->targetFile = $targetFile;
		$this->successful = $successful;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getSourceFile() {
		return $this->sourceFile;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getTargetFile() {
		return $this->targetFile;
	}

	/**
	 * @param \SplFileInfo $targetFile
	 */
	public function setTargetFile(\SplFileInfo $targetFile) {
		$this->targetFile = $targetFile;
	}

	/**
	 * @param boolean $successful
	 */
	public function setSuccessful($successful) {
		$this->successful = (boolean) $successful;
	}

	/**
	 * @return boolean
	 */
	public function getSuccessful() {
		return $this->successful;
	}

	/**
	 * @return boolean
	 */
	public function isSuccessful() {
		return $this->getSuccessful();
	}

	/**
	 * @param string $compiledCode
	 */
	public function setCompiledCode($compiledCode) {
		$this->compiledCode = $compiledCode;
	}

	/**
	 * @return string
	 */
	public function getCompiledCode() {
		return $this->compiledCode;
	}

	/**
	 * @param array $errors
	 */
	public function setErrors(array $errors) {
		$this->errors = $errors;
	}

	/**
	 * Add an error
	 *
	 * @param Error $error
	 */
	public function addError(Error $error) {
		$this->errors[] = $error;
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @param array $warnings
	 */
	public function setWarnings(array $warnings) {
		$this->warnings = $warnings;
	}

	/**
	 * Add a warning
	 *
	 * @param Warning $warning
	 */
	public function addWarning(Warning $warning) {
		$this->warnings[] = $warning;
	}

	/**
	 * @return array
	 */
	public function getWarnings() {
		return $this->warnings;
	}

	/**
	 * Add a message to the result and decide whether it is an error or a warning
	 *
	 * @param Message $message
	 * @throws \InvalidArgumentException
	 */
	public function addMessage(Message $message) {
		if ($message instanceof Warning) {
			$this->addWarning($message);
		} elseif ($message instanceof Error) {
			$this->addError($message);
		} else {
			throw new \InvalidArgumentException('Message must be either a warning or an error,  but is a ' . get_class($message), 1355353270);
		}
	}

}
?>

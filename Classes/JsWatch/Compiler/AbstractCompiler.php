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
 * Abstract base class for a javascript compiler
 */
abstract class AbstractCompiler implements CompilerInterface {

	/**
	 * Compile a javascript file
	 *
	 * @param \SplFileInfo $sourceFile
	 * @param string $targetFileName
	 * @return CompilationResult
	 * @throws \JsWatch\Exception\InvalidPathException
	 */
	public function compileFile(\SplFileInfo $sourceFile, $targetFileName = NULL) {
		if (!$sourceFile->isFile()) {
			throw new \JsWatch\Exception\InvalidPathException('The given file "' . $sourceFile->getPathname() . '" does not exist or isn\'t a file.', 1353619162);
		}

		if ($targetFileName === NULL) {
			$targetFileName = $this->getTargetFileName($sourceFile);
		}

		$targetFile = new \SplFileInfo($targetFileName);

		$compilerOptions = $this->getCompilerOptions($sourceFile, $targetFile);

		return $this->runCompiler($sourceFile, $targetFile, $compilerOptions);
	}

	/**
	 * Get the proposed target file name for a given file
	 *
	 * @param \SplFileInfo $sourceFile
	 * @return string
	 */
	public function getTargetFileName(\SplFileInfo $sourceFile) {
		return $sourceFile->getPath() . '/' . $sourceFile->getBasename('.js') . '.min.js';
	}

	/**
	 * Get options for the compiler
	 *
	 * @param \SplFileInfo $sourceFile
	 * @param \SplFileInfo $targetFile
	 * @return array
	 */
	protected function getCompilerOptions(\SplFileInfo $sourceFile, \SplFileInfo $targetFile) {
		return array();
	}

	/**
	 * Run the compiler on the given file and write the result to the target file
	 *
	 * @param \SplFileInfo $file
	 * @param \SplFileInfo $targetFile
	 * @param array $options
	 * @return CompilationResult
	 */
	protected abstract function runCompiler(\SplFileInfo $file, \SplFileInfo $targetFile, array $options = array());
}

?>

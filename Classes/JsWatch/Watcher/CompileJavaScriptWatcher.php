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

namespace JsWatch\Watcher;

use JsWatch\Compiler as Compiler;

/**
 * Handle changes to javascript files and compile them
 */
class CompileJavaScriptWatcher implements WatcherInterface {

	/**
	 * @var Compiler\GoogleClosureCompilerService
	 */
	protected $compiler;

	/**
	 * @var Compiler\CompilationResultLogger
	 */
	protected $logger;

	/**
	 *
	 */
	public function __construct() {
		$this->compiler = Compiler\CompilerFactory::getInstance()->getNewCompiler();
		$this->logger = Compiler\CompilationResultLogger::getInstance();
	}

	/**
	 * @param string $fileName
	 * @return bool
	 */
	public function watchesFile($fileName) {
		return preg_match('/(?<![.-]min)\.js$/', $fileName) === 1;
	}

	/**
	 * Check whether a javascript file needs compiling, i.e. the source file is newer than the target file
	 *
	 * @param string $fileName
	 * @return bool
	 */
	public function checkFile($fileName) {
		$file = new \SplFileInfo($fileName);
		$targetFile = new \SplFileInfo($this->compiler->getTargetFileName($file));
		return $targetFile->getMTime() > $file->getMTime();
	}

	/**
	 * Compile a javascript file
	 *
	 * @param string $fileName
	 */
	public function processFile($fileName) {
		$this->logger->info('Detected change for ' . $fileName . ', compiling...');
		try {
			$result = $this->compiler->compileFile(new \SplFileInfo($fileName));
			$this->logger->logCompilationResult($result);
		} catch (\JsWatch\Exception\CommandExecutionException $e) {
			$this->logger->critical($e->getMessage());
		}
	}
}

?>

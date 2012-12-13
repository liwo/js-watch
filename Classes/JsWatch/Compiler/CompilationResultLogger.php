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
 * Special class to log compilation results
 */
class CompilationResultLogger extends \JsWatch\Logger {

	/**
	 * Log a compilation result. This logs a generic status message and all warnings and errors.
	 *
	 * @param CompilationResult $result
	 */
	public function logCompilationResult(CompilationResult $result) {
		if ($result->isSuccessful()) {
			$this->ok('Successfully compiled ' . $result->getSourceFile()->getPathname(). ' to ' . $result->getTargetFile()->getPathname());
		} else {
			$this->error('Failed compiling ' . $result->getSourceFile()->getPathname());
		}

		/** @var Error $error */
		foreach ($result->getErrors() as $error) {
			$this->error((string) $error);
		}

		/** @var Warning $warning */
		foreach ($result->getWarnings() as $warning) {
			$this->warn((string) $warning);
		}
	}

}

?>

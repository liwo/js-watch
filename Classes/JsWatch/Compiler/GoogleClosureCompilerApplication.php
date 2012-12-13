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
 * Compile javascript with a local copy of google closure compiler
 */
class GoogleClosureCompilerApplication extends AbstractGoogleClosureCompiler {


	/**
	 * Run the compiler on the given file and write the result to the target file
	 *
	 * @param \SplFileInfo $file
	 * @param \SplFileInfo $targetFile
	 * @param array $options
	 * @return CompilationResult
	 */
	protected function runCompiler(\SplFileInfo $file, \SplFileInfo $targetFile, array $options = array()) {
		$options = array_merge($options, array(
			'logging_level' => 'SEVERE',
			'summary_detail_level' => 0,
		));
		$options['js'] = $file->getPathname();
		$options['js_output_file'] = $targetFile->getPathname();

		$command = 'java -jar ' . escapeshellarg(__DIR__ . '/../../../Vendor/GoogleClosureCompiler/compiler.jar');
		array_walk($options, function($value, $option) use (&$command) {
			$command .= ' --' . $option . ' ' . escapeshellarg($value);
		});

		exec($command . ' 2>&1', $output, $exitCode);

		$result = $this->parseResponse($file, $targetFile, $exitCode, $output);

		return $result;

	}

	/**
	 * Parse the response from closure compiler application into a CompilationResult
	 *
	 * @param \SplFileInfo $sourceFile
	 * @param \SplFileInfo $targetFile
	 * @param integer $exitCode
	 * @param array $outputLines
	 * @return CompilationResult
	 * @throws \JsWatch\Exception\CommandExecutionException
	 */
	protected function parseResponse(\SplFileInfo $sourceFile, \SplFileInfo $targetFile, $exitCode, array $outputLines) {
		$result = new CompilationResult($sourceFile, $targetFile);

		if ($exitCode === 0) {
			$result->setSuccessful(TRUE);
			$result->setCompiledCode(file_get_contents($targetFile->getPathname()));
		}

		for ($i = 0; $i + 2 < count($outputLines); $i += 4) {
			if (preg_match('/^(?<file>[\w.\/_~-]+):(?<line>\d+): (?<type>[A-Z]+) - (?<message>.*)$/', $outputLines[$i], $parsedMessage) !== 1) {
				throw new \JsWatch\Exception\CommandExecutionException('Could not parse compiler response', 1355352825);
			}
			switch ($parsedMessage['type']) {
				case 'WARNING':
					$messageClass = __NAMESPACE__ . '\\Warning';
					break;

				case 'ERROR':
					$messageClass = __NAMESPACE__ . '\\Error';
					break;

				default:
					throw new \JsWatch\Exception\CommandExecutionException('Could not parse compiler response: unknown type ' . $parsedMessage['type'], 1355352906);
			}
			$message = new $messageClass($parsedMessage['message'], $parsedMessage['line'], strlen($outputLines[$i + 2]), $outputLines[$i + 1]);
			$result->addMessage($message);
		}

		return $result;
	}
}

?>

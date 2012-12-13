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
 * Compiles javascript code using googles closure compiler service
 */
class GoogleClosureCompilerService extends AbstractGoogleClosureCompiler {

	/**
	 * Run the compiler on the given file and write the result to the target file
	 *
	 * @param \SplFileInfo $file
	 * @param \SplFileInfo $targetFile
	 * @param array $options
	 * @return CompilationResult
	 * @throws \JsWatch\Exception\CommandExecutionException
	 */
	protected function runCompiler(\SplFileInfo $file, \SplFileInfo $targetFile, array $options = array()) {
		$options = array_merge($options, array(
			'output_format' => 'json',
			'output_info' => array(
				'compiled_code',
				'warnings',
				'errors',
			),
		));
		$options['js_code'] = file_get_contents($file->getPathname());

		$curlHandle = curl_init('http://closure-compiler.appspot.com/compile');
		curl_setopt($curlHandle, CURLOPT_POST, TRUE);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type' => 'application/x-www-form-urlencoded'));
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);

		$parsedOptions = '';
		array_walk($options, function ($value, $option) use (&$parsedOptions) {
			foreach ((array) $value as $singleValue) {
				$parsedOptions .= $option . '=' . rawurlencode($singleValue) . '&';
			}
		});
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, trim($parsedOptions, '&'));

		$jsonResponse = curl_exec($curlHandle);
		if (curl_errno($curlHandle) !== 0) {
			$error = curl_error($curlHandle);
			curl_close($curlHandle);
			throw new \JsWatch\Exception\CommandExecutionException('Sending request to google closure compiler service failed: ' . $error, 1353627327);
		}
		curl_close($curlHandle);

		$response = json_decode($jsonResponse);
		if (!is_object($response)) {
			throw new \JsWatch\Exception\CommandExecutionException('Decoding the compiler result failed', 1355177681);
		}

		$result = $this->parseResponse($file, $targetFile, $response);

		if ($result->isSuccessful()) {
			file_put_contents($targetFile->getPathname(), $result->getCompiledCode());
		}

		return $result;
	}

	/**
	 * Parse the response of the compiler service
	 *
	 * @param \SplFileInfo $sourceFile
	 * @param \SplFileInfo $targetFile
	 * @param object $response
	 * @return CompilationResult
	 * @throws \JsWatch\Exception\CommandExecutionException
	 */
	protected function parseResponse(\SplFileInfo $sourceFile, \SplFileInfo $targetFile, $response) {
		if (isset($response->serverErrors)) {
			throw new \JsWatch\Exception\CommandExecutionException('Error calling google closure compiler service. Server said: ' . $response->serverErrors[0]->code . ': ' . $response->serverErrors[0]->error, 1355180897);
		}

		$result = new CompilationResult($sourceFile, $targetFile);

		if (!empty($response->compiledCode)) {
			$result->setSuccessful(TRUE);
			$result->setCompiledCode($response->compiledCode);
		}

		if (isset($response->errors) && is_array($response->errors)) {
			foreach ($response->errors as $error) {
				$result->addError(new Error($error->error, $error->lineno, $error->charno, $error->line));
			}
		}

		if (isset($response->warnings) && is_array($response->warnings)) {
			foreach ($response->warnings as $warning) {
				$result->addWarning(new Warning($warning->warning, $warning->lineno, $warning->charno, $warning->line));
			}
		}

		return $result;
	}

}

?>

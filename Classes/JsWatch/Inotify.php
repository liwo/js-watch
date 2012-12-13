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
 * Wraps an inotify process
 */
class Inotify {

	/**
	 * Path to inotifywait executable
	 *
	 * @var string
	 */
	protected $executablePath;

	/**
	 * The path to monitor with inotify
	 *
	 * @var string
	 */
	protected $monitoredPath;

	/**
	 * The stdout of the inotifywait process
	 *
	 * @var resource
	 */
	protected $stdOut = NULL;

	/**
	 *
	 * @param string $path The path to monitor
	 */
	public function __construct($path) {
		$executablePath = exec('which inotifywait');
		if (empty($executablePath)) {
			throw new \JsWatch\Exception\MissingDependencyException('The program inotifywait could not be found in your path.', 1353535595);
		}
		$this->executablePath = $executablePath;

		if (!file_exists($path)) {
			throw new \JsWatch\Exception\InvalidPathException('The path "' . $path . '" does not exist.', 1353536088);
		}
		$this->monitoredPath = $path;
	}

	/**
	 *
	 */
	public function __destruct() {
		$this->stop();
	}

	/**
	 * Start the inotifywait process and monitor the given directory for changes
	 *
	 * @return Inotify Self for method call chaining
	 * @throws Exception\InvalidPathException if the path does not exist
	 * @throws Exception\ProcessAlreadyRunningException if the process is already running
	 */
	public function start() {
		if (is_resource($this->stdOut)) {
			throw new \JsWatch\Exception\ProcessAlreadyRunningException('The inotify process is already running on path "' . $this->monitoredPath . '".', 1353536039);
		}

		$stdOut = popen($this->getInotifyWaitCommand(), 'r');
		if ($stdOut === FALSE) {
			throw new \JsWatch\Exception\CommandExecutionException('Unable to start inotifywait process.', 1353536690);
		}

		$this->stdOut = $stdOut;

		return $this;
	}

	/**
	 * Stop the currently running process
	 *
	 * @return Inotify Self for method call chaining
	 */
	public function stop() {
		if (is_resource($this->stdOut)) {
			pclose($this->stdOut);
			$this->stdOut = NULL;
		}

		return $this;
	}

	/**
	 * Get the next changed file. This blocks until there is a file available.
	 *
	 * @return string
	 */
	public function getChangedFile() {
		return trim(fgets($this->stdOut));
	}

	/**
	 *
	 *
	 * @param string $path
	 * @return string
	 */
	protected function getInotifyWaitCommand() {
		$arguments = '-e CLOSE_WRITE,MOVED_TO -q -m -r --format \'%w%f\'';
		return $this->executablePath . ' ' . $arguments . ' ' . escapeshellarg($this->monitoredPath);
	}
}

?>

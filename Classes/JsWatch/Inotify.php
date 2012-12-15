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
	 * @var resource
	 */
	protected $process;

	/**
	 * The stdout of the inotifywait process
	 *
	 * @var resource
	 */
	protected $stdOut = NULL;

	/**
	 *
	 * @param string $path The path to monitor
	 * @throws Exception\MissingDependencyException if inotifywait is not available
	 * @throws Exception\InvalidPathException if the path does not exist
	 */
	public function __construct($path) {
		$executablePath = exec('which inotifywait');
		if (empty($executablePath)) {
			throw new Exception\MissingDependencyException('The program inotifywait could not be found in your path.', 1353535595);
		}
		$this->executablePath = $executablePath;

		if (!file_exists($path)) {
			throw new Exception\InvalidPathException('The path "' . $path . '" does not exist.', 1353536088);
		}
		$this->monitoredPath = $path;

		// Workaround. In case of a fatal error __destruct is not called but shutdown functions are. (http://www.php.net/manual/en/language.oop5.decon.php#108598)
		// Without this, the process hangs forever in case of a fatal error as inotifywait is not terminated
		register_shutdown_function(array($this, '__destruct'));
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
	 * @throws Exception\CommandExecutionException if the process could not be started
	 * @throws Exception\ProcessAlreadyRunningException if the process is already running
	 */
	public function start() {
		if (is_resource($this->process)) {
			throw new Exception\ProcessAlreadyRunningException('The inotify process is already running on path "' . $this->monitoredPath . '".', 1353536039);
		}

		$process = proc_open($this->getInotifyWaitCommand(), array(1 => array('pipe', 'w')), $pipes);
		if ($process === FALSE) {
			throw new Exception\CommandExecutionException('Unable to start inotifywait process.', 1353536690);
		}

		$this->process = $process;
		$this->stdOut = $pipes[1];

		return $this;
	}

	/**
	 * Stop the currently running process
	 *
	 * @return Inotify Self for method call chaining
	 */
	public function stop() {
		if (is_resource($this->process)) {
			fclose($this->stdOut);
			// inotifywait does not exit it its STDOUT is closed, it needs to be killed explicitly
			proc_terminate($this->process);
			proc_close($this->process);
			$this->stdOut = NULL;
			$this->process = NULL;
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
	 * @return string
	 */
	protected function getInotifyWaitCommand() {
		$arguments = '-e CLOSE_WRITE,MOVED_TO -q -m -r --format \'%w%f\'';
		return $this->executablePath . ' ' . $arguments . ' ' . escapeshellarg($this->monitoredPath);
	}
}

?>

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
 * Monitor a path in the filesystem for changes to files and call registered callback functions
 */
class FileSystemMonitor {

	/**
	 * Path to monitor
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Inotify watcher
	 *
	 * @var Inotify
	 */
	protected $inotify;

	/**
	 * The registered watchers
	 *
	 * @var array
	 */
	protected $watchers = array();

	/**
	 * @param string $path Path to monitor for changes
	 * @throws Exception\InvalidPathException if the given path does not exist
	 */
	public function __construct($path = '.') {
		if (!file_exists($path)) {
			throw new \JsWatch\Exception\InvalidPathException('The path "' . $path . '" does not exist', 1353534222);
		}

		$this->path = $path;

		$this->inotify = new Inotify($this->path);
	}

	/**
	 * Add a watcher
	 *
	 * @param Watcher\WatcherInterface $watcher
	 */
	public function addWatcher(\JsWatch\Watcher\WatcherInterface $watcher) {
		$this->watchers[] = $watcher;
	}

	/**
	 * Run the monitoring. This method does not return until the underlying inotify process is somehow stopped.
	 */
	public function run() {
		$this->inotify->start();

		$this->callWatchersForAllFiles();

		while (($file = $this->inotify->getChangedFile()) !== FALSE) {
			$this->callWatchersForFile($file, TRUE);
		}
	}

	/**
	 * Iterate over all monitored files and call the appropriate watchers on them
	 */
	protected function callWatchersForAllFiles() {
		$recursiveDirectoryIterator = new \RecursiveDirectoryIterator($this->path);
		foreach (new \RecursiveIteratorIterator($recursiveDirectoryIterator, \RecursiveIteratorIterator::LEAVES_ONLY) as $fileName => $file) {
			$this->callWatchersForFile($fileName);
		}
	}

	/**
	 * Call all watchers that feel responsible for a file
	 *
	 * @param string $file
	 * @param bool $forceProcessing Force processing of the file. Settings this to TRUE skips the check whether the file needs to be processed
	 */
	protected function callWatchersForFile($file, $forceProcessing = FALSE) {
		/** @var Watcher\WatcherInterface $watcher */
		foreach ($this->watchers as $watcher) {
			if ($watcher->watchesFile($file) && ($forceProcessing || !$watcher->checkFile($file))) {
				$watcher->processFile($file);
			}
		}
	}
}

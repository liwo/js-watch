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

/**
 * Interface defining a watcher
 */
interface WatcherInterface {

	/**
	 * Determines whether the watcher watches a specific file
	 *
	 * @param string $fileName
	 * @return boolean
	 */
	public function watchesFile($fileName);

	/**
	 * Check whether a file is up to date
	 *
	 * @param string $fileName
	 * @return bool
	 */
	public function checkFile($fileName);

	/**
	 * Process the given file
	 *
	 * @param string $fileName
	 * @return void
	 */
	public function processFile($fileName);
}

?>
#!/usr/bin/php
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

// This is necessary to register signal handlers, which are in turn needed to cleanly exit the script on Ctrl+C (SIGINT)
// See http://php.net/manual/en/function.pcntl-signal.php for details
declare(ticks = 1);

try {
	require_once(__DIR__ . '/Classes/JsWatch/Bootstrap.php');
	Bootstrap::run();

	$workingDirectory = rtrim(getcwd(), '/') . '/';

	//$monitor = new FileSystemMonitor($workingDirectory);
	$monitor = new FileSystemMonitor();
	$monitor->addWatcher(new Watcher\CompileJavaScriptWatcher());
	$monitor->run();
} catch (Exception\MissingDependencyException $e) {
	Logger::getInstance()->critical('Missing dependency: ' . $e->getMessage());
	exit(1);
} catch (\Exception $e) {
	Logger::getInstance()->critical('Uncaught exception: ' . $e);
	exit(2);
}

#!/usr/bin/php -d phar.readonly=0
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

if (!Phar::canWrite()) {
	die ("ERROR: Phar is not writeable\n");
}
unlink('js-watch.phar');
$phar = new Phar('js-watch.phar');
$phar->startBuffering();
$phar->buildFromDirectory(__DIR__, '#^' . preg_quote(__DIR__, '#') . '/(?:Classes)/#');
$indexScript = file_get_contents('js-watch.php');
$phar['js-watch.php'] = substr($indexScript, strpos($indexScript, PHP_EOL) + 1);
//$phar->setStub($phar->createDefaultStub('js-watch.php'));
$phar->setStub(<<<EOS
<?php
	Phar::mapPhar(__FILE__);
	require('phar://' . __FILE__ . '/js-watch.php');
	__HALT_COMPILER();
?>
EOS
);
$phar->stopBuffering();


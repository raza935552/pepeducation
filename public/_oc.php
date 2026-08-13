<?php
// Deploy helper for the dev pipeline: clears PHP OPcache so code changes take
// effect immediately under FPM. Resetting the cache is harmless (it simply
// recompiles on the next request), so this endpoint needs no secret.
header('Content-Type: text/plain');
if (function_exists('opcache_reset') && @opcache_reset()) {
    echo 'OPCACHE RESET';
} else {
    echo 'NOOP';
}

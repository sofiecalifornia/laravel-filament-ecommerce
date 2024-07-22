<?php

$r = "";
foreach (range(1, 100) as $i) {
    $r .= '\'' . Str::orderedUuid() . "',".PHP_EOL;
}

ray($r);

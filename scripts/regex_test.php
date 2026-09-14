<?php
$html = file_get_contents(__DIR__ . '/debug_classes.html');
$marker = 'IntegrationClass-1786976396';
$pos = strpos($html, $marker);
echo 'pos=' . ($pos === false ? 'false' : $pos) . PHP_EOL;
$start = max(0, $pos - 400);
$snip = substr($html, $start, 800);
file_put_contents(__DIR__ . '/snippet.txt', $snip);
$pattern = '/action="(?:https?:\/\/[^\"]*)?(\/classes\/(\d+))"/';
if (preg_match($pattern, $snip, $m)) {
    echo 'match: ' . $m[1] . PHP_EOL;
    var_export($m);
} else {
    echo 'no match' . PHP_EOL;
}

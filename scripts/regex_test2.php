<?php
$html = file_get_contents(__DIR__ . '/dump_classes.html');
$name = 'IntegrationClassV2-1786977244';
$pattern = '/' . preg_quote($name, '/') . '.{0,800}?action="[^"]*\/classes\/(\d+)"/s';
if (preg_match($pattern, $html, $m)) {
    echo "matched id={$m[1]}\n";
} else {
    echo "no match for pattern: $pattern\n";
    // try href pattern
    $pattern2 = '/' . preg_quote($name, '/') . '.{0,800}?href="[^"]*\/classes\/(\d+)"/s';
    if (preg_match($pattern2, $html, $m2)) echo "matched href id={$m2[1]}\n"; else echo "no href match\n";
}

<?php
$html = file_get_contents(__DIR__ . '/dump_classes.html');
$name = 'IntegrationClassV2-1786977244';
$pos = strpos($html, $name);
echo "pos_name=$pos\n";
$pos_action = strpos($html, 'action="', $pos);
echo "pos_action=$pos_action\n";
$pos_href = strpos($html, 'href="', $pos);
echo "pos_href=$pos_href\n";
if ($pos !== false && $pos_action !== false) echo "distance=" . ($pos_action - $pos) . "\n";
$snippet = substr($html, $pos, 1200);
file_put_contents(__DIR__ . '/debug_snip.txt', $snippet);
echo "Wrote debug_snip.txt\n";
<?php
require __DIR__ . '/../app/Models/ExamMark.php';

use App\Models\ExamMark;

$cases = [
    [0, 100],
    [100, 100],
    [50, 100],
    [40, 100],
    [33, 100],
    [32.5, 100],
    [89.9, 100],
    [90, 100],
    [null, 100],
    [50, 0],
    [-10, 100],
    [120, 100],
];

foreach ($cases as [$obt, $tot]) {
    $g = ExamMark::computeGrade($obt, $tot);
    echo "obtained={$obt} total={$tot} => grade={$g}\n";
}

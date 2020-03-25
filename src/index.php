<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: julius hernandez alvarado
 * Date: 3/23/2020
 * Time: 8:46 PM
 *
 * Insight coding challenge built with PHP 7.4 ❤🤗😊👌😍🥰🚀🐱‍🏍🐱‍👤🐱‍👤🐱‍👤🐱‍👤🐱‍👤
 */

use dataphp\ProcessComplaints;

// manually require classes
require 'classes/DataStream.php';
require 'classes/ProcessComplaints.php';

$pathToCsv = '../input/s_complaints.csv';

$complaintProcessor = new ProcessComplaints($pathToCsv);

// start stopwatch
$startTime = microtime(true);
// invoke the PHP program and track mem
$m = number_format($complaintProcessor->compute());
// end stopwatch
$endTime = number_format($startTime - microtime(true));

// output to the CLI runtime and mem used
echo "\nTotal time to run program = $endTime\n";
echo "\nMost memory used = $m\n";
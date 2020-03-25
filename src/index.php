<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: julius hernandez alvarado
 * Date: 3/23/2020
 * Time: 8:46 PM
 *
 * Insight coding challenge built with PHP 7.4
 *
 * branch qb
 */

use dataphp\ProcessComplaints;

// manually require classes
require 'classes/DataStream.php';
require 'classes/ProcessComplaints.php';

$pathToCsv = '../input/complaints.csv';

$complaintProcessor = new ProcessComplaints($pathToCsv);

// let's see how much memory and time is required just to scan the data with no app logic
$startTime = microtime(true);
$m = number_format($complaintProcessor->compute());
$endTime = number_format($startTime - microtime(true));
echo "\nTotal time to run program = $endTime\n";
echo "\nMost memory used = $m\n";
<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: julius hernandez alvarado
 * Date: 3/23/2020
 * Time: 9:01 PM
 */

namespace dataphp;

class DataStream
{
    /**
     * Separate the raw input data from the OOP computation
     *
     * @param string $pathToCsv
     *
     * @return \Generator
     */
    public static function genStream(string $pathToCsv): \Generator {
        try {
            if(($handle = fopen($pathToCsv, 'r')) !== false) {
                while(($data = fgetcsv($handle)) !== false) {
                    yield $data;
                }
            }
            else {
                $ml = __METHOD__ . ' line: ' . __LINE__;
                throw new \Exception('_> insight-cc error: could not open csv ~' . $ml);
            }
        } catch(\Throwable $err) {
            exit($err->getMessage());
        }
    }
}
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
            
            fclose($handle);
            
        } catch(\Throwable $err) {
            exit($err->getMessage());
        }
    }
    
    /**
     * to maintain separation between raw data & OOP computing
     * I did all this work... 🤔
     * ... I hope PHP doesn't ding my codes time complexity because of this
     *
     * @param $stream
     * @param array $data
     */
    public static function writeToDisk($stream, array $data) {
        fputcsv($stream, $data);
    }
}
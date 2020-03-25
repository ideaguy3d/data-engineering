<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: julius hernandez alvarado
 * Date: 3/23/2020
 * Time: 9:26 PM
 */

namespace dataphp;


class ProcessComplaints
{
    /**
     * The field names for the output file
     * will act like an ENUM
     * @var object
     */
    private object $reportFields;
    
    /**
     * The path to the complains csv
     * @var string
     */
    private string $pathToCsv;
    
    public function __construct(string $pathToCsv) {
        // keeping var names short & sweet
        $this->reportFields = new class() {
            public string $pro = 'product';
            public string $y = 'year';
            public string $complain = 'product_total_complaints';
            public string $biz = 'total_company_complaints';
            public string $pct = 'company_highest_percent';
        };
        $this->pathToCsv = $pathToCsv;
    }
    
    public function compute(): int {
        $mostMem = memory_get_usage();
        /*******************************************
         ************** THE MAIN LOOP *************
         ******************************************/
        foreach(DataStream::genStream($this->pathToCsv) as $k => $row) {
            // print every 10 recs
            if($k % 1000 === 0) {
                $mem = memory_get_usage();
                if($mem > $mostMem) {
                    $mostMem = $mem;
                }
                $row = print_r($row, true);
                echo "\n$mem | $k: $row\n";
            }
        }
        return $mostMem;
    }
}
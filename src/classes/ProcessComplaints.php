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
     * The result of the program
     * @var array
     */
    private array $report;
    
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
        $this->report [] = array_values((array)$this->reportFields);
        $this->pathToCsv = $pathToCsv;
    }
    
    /**
     * Will compute for each [product] and [year] that complaints were received,
     * - the total number of complaints,
     * - number of companies receiving a complaint
     * - and the highest percentage of complaints directed at a single company.
     *
     * @return int
     */
    public function compute(): int {
        // HARD CODED index's just to get a solution built out real quick
        //TODO: dynamically find index of fields just in case column order changes
        $_company = 7;
        $_date = 0;
        $_product = 1;
        
        $reportStruct = new class() {
            public array $co;
            public array $y;
            public array $pro;
            public array $biz;
        };
        
        $mostMem = memory_get_usage();
        // track mem usage & print every 1,000 recs
        $trackMemLambda = function() use (&$mostMem, &$k, &$row): void {
            $tempRow = $row;
            unset($row);
            if($k % 1000 === 0) {
                $mem = memory_get_usage();
                if($mem > $mostMem) {
                    $mostMem = $mem;
                }
                $row = print_r($tempRow, true);
                echo "\n$mem | $k: $row\n";
            }
        };
        
        /*******************************************
         ************** THE MAIN LOOP *************
         ******************************************/
        foreach(DataStream::genStream($this->pathToCsv) as $k => $row) {
            // skip header row
            if(0 === $k) continue;
            $company = trim($row[$_company]);
            $year = trim(substr($row[$_date], 0, 4));
            // check for commas
            $product = trim($row[$_product]);
            $key = $product . "_$year";
            
            if(isset($reportStruct->pro[$key])) {
                $reportStruct->pro[$key]++;
            }
            else {
                $reportStruct->pro[$key] = 1;
            }
            
            $trackMemLambda();
            $debug = 1;
        }
        return $mostMem;
    }
}
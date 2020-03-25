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
     * - the total number of complaints
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
        
        // get initial mem
        $mostMem = memory_get_usage();
        
        // struct to assist report output
        $reportStruct = new class() {
            public array $complaint;
            public array $yearBusinessTotal;
            public array $product;
            public array $business;
        };
        
        /** Utility Lambda Functions **/
        // track mem usage & print every 1,000 recs
        $trackMem = function() use (&$mostMem, &$k, &$row): void {
            // create a copy of the data row
            $tempRow = $row;
            // dereference $row to re-init later
            unset($row);
            
            if($k % 1000 === 0) {
                $mem = memory_get_usage();
                if($mem > $mostMem) {
                    $mostMem = $mem;
                }
                $row = print_r($tempRow, true);
                echo "\n_> memory used = $mem\n on row $k, row data:\n $row\n";
            }
        };
        //TODO: create an exception handler and be sure to unit test this app logic
        $yearBusinessHashIsSet = function($v_year, $v_business) use ($reportStruct): bool {
            return isset($reportStruct->yearBusinessTotal[$v_year])
                && isset($reportStruct->yearBusinessTotal[$v_year][$v_business]);
        };
        
        /*******************************************
         ************** THE MAIN LOOP *************
         ******************************************/
        foreach(DataStream::genStream($this->pathToCsv) as $k => $row) {
            // skip header row
            if(0 === $k) continue;
            
            // cache field Values and sanitize a bit
            $v_business = trim($row[$_company]);
            $v_year = trim(substr($row[$_date], 0, 4));
            //TODO: check for commas
            $v_product = trim($row[$_product]);
            
            // create hash table keys to maintain O(1)
            $keyProduct = $v_product . "_$v_year";
            $keyBusiness = $v_business . "_$v_year";
            
            // track total number of complaints BY product & year
            if(isset($reportStruct->product[$keyProduct])) {
                $reportStruct->product[$keyProduct]++;
            }
            else {
                $reportStruct->product[$keyProduct] = 1;
            }
            
            // track company & year
            if(isset($reportStruct->business[$keyBusiness])) {
                $reportStruct->business[$keyBusiness]++;
            }
            else {
                $reportStruct->business[$keyBusiness] = 1;
            }
            
            // track total number of companies that received complaints BY product & year
            if($yearBusinessHashIsSet($v_year, $v_business)) {
                $reportStruct->yearBusinessTotal[$v_year][$v_business]++;
                $debug = 1;
            }
            else {
                //TODO: create an exception handler and be sure to unit test this app logic
                $reportStruct->yearBusinessTotal[$v_year][$v_business] = 1;
                $debug = 1;
            }
            
            $trackMem();
            $debug = 1;
        
        } // end of main loop
    
        /*foreach($reportStruct->business as $key => $value) {
            $businessByYear = explode('_', $key);
            $reportStruct->yearBusinessTotal[$businessByYear[1]] []= $businessByYear[0];
        }*/
        
        return $mostMem;
    }
}
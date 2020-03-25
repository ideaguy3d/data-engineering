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
     * The path to the complaints csv
     * @var string
     */
    private string $pathToCsv;
    
    private $streamTo;
    
    public function __construct(string $pathToCsv, string $pathToCsvOutput) {
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
        $this->streamTo = fopen($pathToCsvOutput, 'w');
    }
    
    public function __destruct() {
        if(isset($this->streamTo)) {
            fclose($this->streamTo);
        }
    }
    
    /**
     * Will compute for each [product] and [year] that complaints were received,
     * - the total number of complaints
     * - number of companies receiving a complaint
     * - and the highest percentage of complaints directed at a single company.
     *
     * @return int - return the most mem used during the program
     */
    public function compute(): int {
        // get initial mem
        $mostMem = memory_get_usage();
        
        // HARD CODED index's just to get a solution built out real quick
        //TODO: dynamically find index of fields just in case column order changes
        $_company = 7;
        $_date = 0;
        $_product = 1;
        
        // struct to assist report output
        $reportStruct = new class() {
            public array $complaintPct;
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
        // calc highest pct filed
        $calcPct = function(array $curYear): int {
            // round((max($curYear) / count($curYear)) * 100)
            return (int)(round((max($curYear) / count($curYear)) * 100));
        };
        
        /************************************************
         ************** THE MAIN LOOP O(n) *************
         ***********************************************/
        // where n is the number of records in the primary data set
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
            
        } // END OF main loop
        
        // 2nd outer loop where n2 is how many years there are
        // therefore time complexity increases to O(n * n2)
        //-- get the highest percentage filed against 1 company
        foreach($reportStruct->yearBusinessTotal as $key => $value) {
            $curYear = $reportStruct->yearBusinessTotal[$key];
            $reportStruct->complaintPct[$key] = $calcPct($curYear);
            $debug = 1;
        }
        
        // 3rd outer loop where n3 is the number of aggregated year & products
        // (e.g. GROUP BY [year], [product]) e.g. O(n*n2*n3)
        //-- attempt to construct the entire report array with this loop
        foreach($reportStruct->product as $hash => $value) {
            // product year
            $py = explode('_', $hash);
            // results
            $r = [
                'product' => $py[0], 'year' => $py[1], 'product_total_complaints' => $value,
            ];
            $totalCompanyComplaints = count($reportStruct->yearBusinessTotal[(int)$r['year']]);
            /*
            //-- DAMMIT !! this won't work, I need to create a data structure like:
            $mockStruct = [
                '2020' => [
                    'company_foo' => [
                        'product_bar_complaints' => 111,
                    ]
                ],
                '2019' => [
                    'company_foo' => [
                        'product_bar_complaints' => 222
                    ]
                ]
            ];
            
            // my current data structure doesn't map year.company.product.numComplaints
            $complaintPct = array (
              2019 => 67,
              2020 => 100,
            )
            */
            
            //TODO: FIX THIS, the last field is wrong, it's an easy fix that I'll work on tonight
            $companyHighestPct = $reportStruct->complaintPct[(int)$r['year']];
            
            // just test the rest of the logic
            $r [] = $totalCompanyComplaints;
            $r [] = $companyHighestPct;
            $this->report [] = $r;
        }
        
        return $mostMem;
    }
}
<?php

use Illuminate\Foundation\Inspiring;
use Ixudra\Curl\CurlService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('npp-invoice-sender', function() {
        $data = \Excel::selectSheets('Sheet1')
        ->load(
            storage_path('data.xlsx'),
            function ($excelReader) {
                //
            }
        )->get();

        $fixedValue = array(
            'app_id' => 7453014083,
            'app_key' => 13018237,
            'currency' => 'GHS',
            'order_desc' => 'Hello Ningo Prampram resident! You can now conveniently pay your 2018 business or property tax to Ningo-Prampram District Assembly by simply dialing *711*100#',
        );
        $businessInfos = [];
        $skipRowCount = 0;
        if($skipRowCount > 0) {
            $this->comment("Skip $skipRowCount rows...");
        }
        foreach ($data as $key => $row) {
            //Skip row
            if($key+2 <= $skipRowCount) {
                continue;
            }

            if((int)$row['amt'] == 0 || !$row['amt'] || !$row['telephone'] || strlen($row['telephone']) !== 9) {
                $this->comment('skip row '.($key+2).'.('.$row.')');
                continue;
            }
            $arrangedRow = [
                "name" => $row['business'],
                "amount" => (int)$row['amt'],
                "mobile" => "+233".$row['telephone'],
                "order_id" => "jun_7_".(count($businessInfos) + 1)
            ];
            $arrangedRow = array_merge($arrangedRow, $fixedValue);
            array_push($businessInfos, $arrangedRow);
            $curlService = new CurlService;

            $response = $curlService->to('https://www.interpayafrica.com/interapi/CreateInvoice')
                ->withData($arrangedRow)
                ->returnResponseObject()
                ->post();

            $responseContent = json_decode($response->content, true);
            if($response->status != 200 || is_null($responseContent) || !array_key_exists('status_code', $responseContent) || $responseContent['status_code'] != 1) {
                $this->error("npp sender stopped by got a response error($response->status), stopped row: ".($key+2));
                \Log::error("npp sender stopped by got a response error($response->status), stopped row: ".($key+2));
                break;
            }

            $this->info("order_id:".$arrangedRow['order_id'].",row:".($key+2).",Total: ".count($businessInfos).",Response: ".$response->content);
            \Log::notice("order_id:".$arrangedRow['order_id'].",row:".($key+2).",Total: ".count($businessInfos).",Response: ".$response->content);

            sleep(9);

            // if(count($businessInfos) == 10) {
            //  break;
            // }
        }

        $this->info('Done');
})->describe('Send Interpay invoice by contacts in excel sheet');

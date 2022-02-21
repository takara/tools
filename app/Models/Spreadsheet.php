<?php

namespace App\Models;

use Exception;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_CopySheetToAnotherSpreadsheetRequest;
use Illuminate\Config\Repository;

/**
 * https://opendata.corona.go.jp/api/Covid19JapanAll
 */
class Spreadsheet
{
    /**
     * @var self|null
     */
    protected static $instance = null;

    /**
     * @var
     */
    protected $client;

    protected $service = null;

    public function __construct()
    {
        /**
         * @var $config Repository
         */
        $config           = app('config');
        $clientId         = $config->get('app.spreadsheet.client_id');
        $clientEmail      = $config->get('app.spreadsheet.client_email');
        $signingKey       = $config->get('app.spreadsheet.signing_key');
        $signingAlgorithm = $config->get('app.spreadsheet.signing_algorithm');

        $this->client = new Google_Client([
            'client_id'         => $clientId,
            'client_email'      => $clientEmail,
            'signing_key'       => $signingKey,
            'signing_algorithm' => $signingAlgorithm,
        ]);
        $this->client->setScopes([
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Sheets::DRIVE,]);
        $this->client->useApplicationDefaultCredentials();
    }

    public function getService(): Google_Service_Sheets
    {
        if (is_null($this->service)) {
            $this->service = new Google_Service_Sheets($this->client);
        }

        return $this->service;
    }

    public static function getInstance(): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
/*
    public function getSheet()
    {
        $sheet = new Google_Service_Sheets($this->client);
        $sheet_id = '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc';
        $range = '02/08!A2:D37';
        $response = $sheet->spreadsheets_values->get($sheet_id, $range);
        $values = $response->getValues();

        return $values;
    }
*/
    /**
     * @throws Exception
     */
    public function getCreateTodayParam(): array
    {
        /**
         * @var $config Repository
         */
        $config            = app('config');
        $fromSpreadSheetId = $config->get('app.spreadsheet.from_spread_sheet_id');
        $fromSheetIdList   = $config->get('app.spreadsheet.from_sheet_id');
        $sheetTitle        = DatetimeUtil::now()->format('m/d');
        $toSpreadSheetId   = $config->get('app.spreadsheet.to_spread_sheet_id');
        $weekDay           = DatetimeUtil::now()->format('w');
        $fromSheetId       = $fromSheetIdList[$weekDay];
        return [
            'fromSpreadSheetId' => $fromSpreadSheetId,
            'fromSheetId'       => $fromSheetId,
            'sheetTitle'        => $sheetTitle,
            'toSpreadSheetId'   => $toSpreadSheetId,
        ];
    }

    /**
     * @throws Exception
     */
    public function createTodayActivity()
    {
        $service           = $this->getService();
        $param             = $this->getCreateTodayParam();
        $fromSpreadSheetId = $param['fromSpreadSheetId'];
        $fromSheetId       = $param['fromSheetId'];
        $sheetTitle        = $param['sheetTitle'];
        $toSpreadSheetId   = $param['toSpreadSheetId'];

        $sheetId = $this->copySheet($fromSpreadSheetId, $fromSheetId, $sheetTitle, $toSpreadSheetId);

        $rowData = [
            "values" => [
                ["userEnteredValue" => [ "stringValue" => ""] ],
                ["userEnteredValue" => [ "stringValue" => ""] ],
                ["userEnteredValue" => [ "stringValue" => ""] ],
            ]
        ];
        $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'updateCells' => [
                    "start" => [
                        'sheetId' => $sheetId,
                        "rowIndex" => 1,
                        "columnIndex" => 1,
                    ],
                    "rows" => array_fill(0, 36, $rowData),
                    'fields' => 'userEnteredValue',
                ],
            ]
        ]);
        $service->spreadsheets->batchUpdate($toSpreadSheetId, $body);
    }

    public function copySheet(string $fromSpreadSheetId, string $fromSheetId, string $sheetTitle, string $toSpreadSheetId = null): int
    {
        if (is_null($toSpreadSheetId)) {
            $toSpreadSheetId = $fromSpreadSheetId;
        }
        $service = $this->getService();
        $toSpreadsheet = new Google_Service_Sheets_CopySheetToAnotherSpreadsheetRequest();
        $toSpreadsheet->setDestinationSpreadsheetId($toSpreadSheetId);
        $response = $service->spreadsheets_sheets
            ->copyTo(
                $fromSpreadSheetId,
                $fromSheetId,
                $toSpreadsheet
            );
        $toSheetId = $response->getSheetId();
        $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => $toSheetId,
                        'title'   => $sheetTitle,
                        'index'   => 0,
                    ],
                    'fields' => 'title,index',
                ],
            ]
        ]);
        $service->spreadsheets->batchUpdate($toSpreadSheetId, $body);
        return $toSheetId;
    }
}

<?php

namespace App\Models;

use Google_Client;
use Google_Service_Sheets;
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

    public static function getInstance(): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getSheet()
    {
        $sheet = new Google_Service_Sheets($this->client);
        $sheet_id = '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc';
        $range = '02/08!A2:D37';
        $response = $sheet->spreadsheets_values->get($sheet_id, $range);
        $values = $response->getValues();

        return $values;
    }
}

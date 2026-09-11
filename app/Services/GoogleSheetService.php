<?php

namespace App\Services;


use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    protected $service;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('jp-auto-ai-ea7f30a5d2bf.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);
    }

    public function insertSingleRow($spreadsheetId, $range, array $row)
    {
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [$row]
        ]);

        $params = ['valueInputOption' => 'RAW'];

        return $this->service->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
    }
}
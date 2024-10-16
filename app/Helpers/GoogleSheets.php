<?php

namespace App\Helpers;


// use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GoogleSheets{

    private $values;
    private $multiple_ranges = false;

    /**
     * Construct the Google Sheet Client.
     *
     * @param string $spreadsheetId ID of speadsheet, can be found in url https://docs.google.com/spreadsheets/d/{spreadsheetID}/...
     * @param string|array $range This is the sheet name with cell-values range for example $range = 'content-pages!A1:B15' or $range = ['content-pages!A1:B15', '$range = 'other-pages']
     */
    public function __construct($spreadsheetId, $range='Sheet1') {

        $client = new \Google\Client();
        $client->setApplicationName(config('google.application_name'));
        $client->setScopes(config('google.scopes'));
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->refreshToken(config('google.refresh_token'));

        // this will call https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets.values/get#query-parameters
        /* REFS:
            $service: https://github.com/googleapis/google-api-php-client-services/blob/main/src/Sheets.php
            $service->spreadsheets_values = https://github.com/googleapis/google-api-php-client-services/blob/main/src/Sheets/Resource/SpreadsheetsValues.php
            $response = https://github.com/googleapis/google-api-php-client-services/blob/main/src/Sheets/ValueRange.php

        */

        $service = new \Google\Service\Sheets($client);

        if(is_array($range)){
            # Fetch data from multiple sheets

            $this->multiple_ranges = true;
            $sheets = [];
            foreach ($range as $r) {
                $response = $service->spreadsheets_values->get($spreadsheetId, $r);
                $sheets[$r] = collect($response->getValues());
            }
            $this->values = $sheets;
        }
        else{
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $this->values = $response->getValues();
        }
    }


    public function all() : Collection {

        return Collection::make( $this->values );
    }

    public function headings() : array {

        $rows = $this->all();
        if($this->multiple_ranges){
            $array_of_rows = $rows->toArray();
            $header = [];
            foreach ($array_of_rows as $key => $value) {
                $header[$key] = Collection::make( $value )->shift();
            }
        }
        else $header = $rows->shift();

        return $header;
    }

    public function get($format_header = false): Collection
    {
        $rows = $this->all();

        if($this->multiple_ranges){


            $data = [];
            foreach ($rows as $key => $row) {

                // extract header
                $header = $row->shift();

                $data[$key] =  $row->map(function ($item, $index) use ($header, $key, $format_header) {
                    if(count($item) > count($header)){
                        $header = Collection::make($header)->pad(count($item), 'Item '.(count($header)+1));
                    }
                    else{

                        $item = Collection::make($item)->pad(count($header), '');
                    }

                    # Logic to detect empty rows
                    $all_empty = Collection::make($item)->every(function ($value, int $key) {
                        return !( isset($value) && trim($value) !== '' );
                    });
                    if($all_empty) return null; // will be filtered

                    if($format_header){
                        # Make header code-friendly
                        $header = Collection::make($header)->map(function($item){
                            return strtolower( Str::slug( preg_replace('/\s+/', '', $item) ) );
                        });
                    }

                    return Collection::make($header)->combine($item);
                })
                ->filter(function($item){
                    return isset($item);
                })
                ->values();
            }
            return Collection::make($data);
        }
        else{
            $header = $rows->shift();

            return $rows->map(function ($item, $index) use ($header, $format_header) {
                if(count($item) > count($header)){
                    $header = Collection::make($header)->pad(count($item), 'Item '.(count($header)+1));
                }
                else{

                    $item = Collection::make($item)->pad(count($header), '');
                }

                # Logic to detect empty rows
                $all_empty = Collection::make($item)->every(function ($value, int $key) {
                    return !( isset($value) && trim($value) !== '' );
                });
                if($all_empty) return null; // will be filtered

                if($format_header){
                    # Make header code-friendly
                    $header = Collection::make($header)->map(function($item){
                        return strtolower( Str::slug( preg_replace('/\s+/', '', $item) ) );
                    });
                }

                return Collection::make($header)->combine($item);
            })
            ->filter(function($item){
                return isset($item);
            })
            ->values();
        }
    }
}

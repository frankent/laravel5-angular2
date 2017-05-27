<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class Sync extends Command
{
    private $client;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Data ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    private function getRequest ($url)
    {
        $res = $this->client->request('GET', $url);
        $textResponse = (string) $res->getBody();
        return json_decode($textResponse);
    }

    private function encryption ($data)
    {
        return base64_encode(gzencode(json_encode($data)));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentTime = time();
        $damService = 'http://127.0.0.1:81/pressure/sync?site_id=maengron_pressure';
        $uploadDamService = 'http://127.0.0.1:81/pressure/sync';
        $mtpSyncRef = 'http://log.ddnsthai.com/MaengronAPI/api/maengron/sync';

        // Get Sync Ref from MTP
        $mtpSync = $this->getRequest($mtpSyncRef);
        $latestMTPRecord = strtotime($mtpSync->data[0]->timestamp);

        // Get latest record
        $sync = $this->getRequest($damService);
        $startSync = strtotime($sync->start);
        $endSync = strtotime($sync->end);

        // Check new data
        if ($latestMTPRecord <= $startSync) {
            $this->line('No new data #1');
            return false;
        }

        // Has new data start sync
        $this->line('Has new data: ' . $mtpSync->data[0]->timestamp . ' : ' . $sync->start);
        $datetimeFormat = ($latestMTPRecord - $startSync) <= 86400 ? $latestMTPRecord : $startSync;
        do {
            $resyncAgain = false;
            do {
                $syncNext = false;
                $continueSync = date('Y-m-d 00:00:00', $datetimeFormat);
                $mtpService = "http://log.ddnsthai.com/MaengronAPI/api/maengron/sync?timestamp={$continueSync}&limit=300&station=1";
                $rawsData = $this->getRequest($mtpService);
                
                if (empty($rawsData->data) && $currentTime > $datetimeFormat) {
                    $this->error('Sync error : no new data on ' . $continueSync);
                    $syncNext = true;
                    $datetimeFormat += 86400;
                    sleep(2);
                }
            } while($syncNext);

            // Process all data            
            if (empty($rawsData->data)) {
                $this->error('No new data #2 : sync abort');
                return false;
            }

            $newData = array();
            foreach ($rawsData->data as $rec) {
                $recUnix = strtotime($rec->timestamp);
                if ($startSync <= $recUnix) {
                    $newData[$recUnix] = $rec;
                }
            }

            if (empty($newData)) {
                $this->error('No new data #3 : resync');
                $resyncAgain = true;
                $datetimeFormat += 86400;
            } else {
                ksort($newData);
            }

        } while($resyncAgain);

        $param = array(
            'site_id' => 'maengron_pressure',
            'encode' => 1,
            'data' => $this->encryption($newData)
        );

        $uploadResponse = $this->client->request('POST', $uploadDamService, array(
            'form_params' => $param
        ));

        $resp = (string) $uploadResponse->getBody();
        $this->info($resp);
    }
}

<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller {

    private $client;
    public function __construct() {
        $this->client = new Client();
    }

    private function retrieveData($currentDate) {
        $start = date('Y-m-d 00:00:00', $currentDate);
        $end = date('Y-m-d 23:59:59', $currentDate);

        $endPoint = config('endpoint.damService') . "/pressure?site_id=maengron_pressure&start={$start}&end={$end}";
        $resp = $this->client->request('GET', $endPoint);
        $stationData = $resp->getBody();
        $data = (string) $stationData;
        return json_decode($data, true);
    }

    public function getData() {
        $currentDate = time();

        // Yesterday
        $yesterdayData = $this->retrieveData($currentDate - 86400);

        // Today
        $todayData = $this->retrieveData($currentDate);

        $allData = array_merge($yesterdayData['result']['stream'], $todayData['result']['stream']);
        ksort($allData);
        $allData = array_slice($allData, -96, 96, true);

        $first = array_first($allData, function($v, $k) {
            return $v['unix'];
        });

        $last = array_last($allData, function($v, $k) {
            return $v['unix'];
        });

        $forcastLabel = [];
        for($i = 0; $i < 5; $i++) {
            $margin = 60 * 15 * ($i + 1);
            $forcastLabel[] = date('Y-m-d H:i:s', $last['unix'] + $margin);
        }

        return array(
            'range' => array(
                'start' => $first['timestamp'],
                'end' => $last['timestamp']
            ),
            'forcastNode' => $forcastLabel,
            'total' => count($allData),
            'data' => $allData
        );
    }
}
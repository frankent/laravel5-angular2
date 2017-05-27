<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller {

    private $client;
    public function __construct() {
        $this->client = new Client();
    }

    public function getData() {
        $currentDate = time();
        $start = Input::get('start', date('Y-m-d H:i:s', $currentDate - 32400));
        $end = Input::get('end', date('Y-m-d H:i:s',$currentDate));

        $endPoint = config('endpoint.damService') . "/pressure?site_id=maengron_pressure&start={$start}&end={$end}";
        $resp = $this->client->request('GET', $endPoint);
        $stationData = $resp->getBody();
        $data = (string) $stationData;
        $data = json_decode($data, true);

        $last = last($data['result']['stream']);
        $forcastLabel = [];
        for($i = 0; $i < 5; $i++) {
            $margin = 60 * 15 * ($i + 1);
            $forcastLabel[] = date('Y-m-d H:i:s', $last['unix'] + $margin);
        }

        return array(
            'range' => array(
                'start' => $start,
                'end' => $end
            ),
            'forcastNode' => $forcastLabel,
            'total' => count($data['result']['stream']),
            'data' => $data['result']['stream']
        );
    }
}
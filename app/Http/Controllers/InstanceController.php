<?php

namespace App\Http\Controllers;

use App\Models\Cloudware;
use App\Models\Instance;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7;

class InstanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()->instances;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cloudware = Cloudware::find($request->cloudware_id);
        $data = [
            'instanceTriggeredStop' => "stop",
            'startOnCreate' => true,
            'privileged' => false,
            'stdinOpen' => true,
            'tty' => true,
            'readOnly' => false,
            'networkMode' => "bridge",
            'type' => "container",
            'requestedHostId' => "1h5",
            'imageUuid' => "docker:" . $cloudware->image,
            'ports' => ["5678/tcp"],
            'memory' => 134217728 * 4, // 128m*4
        ];
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', config('services.rancher.endpoint') . '/projects/1a5/container', [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
            'json' => $data
        ]);

        $payload = \GuzzleHttp\json_decode($res->getBody());

        $instance = Instance::create([
            'rancher_container_id' => $payload->id,
            'cloudware_id' => $cloudware->id,
            'user_id' => $request->user()->id
        ]);

        $port = null;
        for ($i = 0; $i < 5; $i++) {
            $port = $this->getPulsarPort($instance->rancher_container_id);
            if ($port) {
                break;
            }
            sleep(2);
        }
        if ($port) {
            $ip = $this->getPulsarIp($port['id']);
            $instance->ws = 'ws://' . $ip . ':' .$port['port'];
        }

        return $instance;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function getPulsarPort($rancher_container_id)
    {
        $client = new \GuzzleHttp\Client();
        $url = config('services.rancher.endpoint') . '/projects/1a5/containers/' . $rancher_container_id . '/ports';
        $res = $client->request('GET', $url, [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
        ]);

        $payload = \GuzzleHttp\json_decode($res->getBody());
        if (!$payload->data[0]->publicPort) {
            return null;
        }

        $port = $payload->data[0]->publicPort;
        return ['id' => $payload->data[0]->publicIpAddressId, 'port' => $port];
    }

    private function getPulsarIp($rancher_port_id)
    {
        $client = new \GuzzleHttp\Client();
        $url = config('services.rancher.endpoint') . '/projects/1a5/ipaddresses/' . $rancher_port_id;
        $res = $client->request('GET', $url, [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
        ]);

        $payload = \GuzzleHttp\json_decode($res->getBody());
        $ip = $payload->address;
        return $ip;
    }

}

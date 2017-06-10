<?php

namespace App\Http\Controllers;

use App\Models\Cloudware;
use App\Models\Instance;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7;
use Illuminate\Validation\Rules\In;

class InstanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instances = $request->user()->instances;
        foreach ($instances as $i) {
            $i->cloudware;
        }
        return $instances;
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
            //'networkMode' => "bridge",
            'type' => "container",
//            'requestedHostId' => "1h5",
            'imageUuid' => "docker:" . $cloudware->image,
            'ports' => ["5678/tcp"],
            'memory' => 134217728 * 4, // 128m*4
            'labels' => [
                "io.rancher.scheduler.affinity:host_label" => "cloudware=true"
            ],
            'command' => ['startxfce4']
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
        for ($i = 0; $i < 10; $i++) {
            $ip = $this->getPrivateIp($instance->rancher_container_id);
            if ($ip) {
                break;
            }
            sleep(1);
        }
        if ($ip) {
            $client = new \LinkORB\Component\Etcd\Client(config('services.etcd.server'));
            $client->set('/traefik/backends/pulsar-'.$instance->id.'/servers/server1/url', 'http://' . $ip . ':5678');
            $client->set('/traefik/frontends/pulsar-'.$instance->id.'/routes/test_1/rule', 'PathPrefix:/pulsar-'.$instance->id);
            $client->set('/traefik/frontends/pulsar-'.$instance->id.'/backend', 'pulsar-'.$instance->id);

//            $client->set('/traefik/backends/fs-'.$instance->id.'/servers/server1/url', 'http://' . $ip . ':5679');
//            $client->set('/traefik/frontends/fs-'.$instance->id.'/routes/test_1/rule', 'PathPrefix:/fs-'.$instance->id);
//            $client->set('/traefik/frontends/fs-'.$instance->id.'/backend', 'fs-'.$instance->id);

            $client->set('/traefik/backends/vfs-'.$instance->id.'/servers/server1/url', 'http://' . $ip . ':5680');
            $client->set('/traefik/frontends/vfs-'.$instance->id.'/routes/test_1/rule', 'PathPrefixStrip:/vfsproxy/vfs-'.$instance->id);
            $client->set('/traefik/frontends/vfs-'.$instance->id.'/backend', 'vfs-'.$instance->id);
        }
        sleep(8);
        $instance->ws = 'ws://' . config('services.proxy.server') . '/pulsar-' . $instance->id;

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
        $instance = Instance::find($id);
        $instance->cloudware;
        $instance->ws = 'ws://' . config('services.proxy.server') . '/pulsar-' . $instance->id;
        return $instance;
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
        $instance = Instance::find($id);
        if (!$instance) {
            return Response::json([
                'error' => 'instance not found'
            ], 404);
        }

        /* delete container */
        $client = new \GuzzleHttp\Client();
        $res = $client->request('DELETE', config('services.rancher.endpoint') . '/projects/1a5/containers/' . $instance->rancher_container_id, [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
        ]);

        /* delete proxy config */
        $client = new \LinkORB\Component\Etcd\Client(config('services.etcd.server'));
        $client->rm('/traefik/backends/pulsar-'.$instance->id.'/servers/server1/url');
        $client->rmdir('/traefik/backends/pulsar-'.$instance->id, true);
        $client->rm('/traefik/frontends/pulsar-'.$instance->id.'/routes/test_1/rule');
        $client->rm('/traefik/frontends/pulsar-'.$instance->id.'/backend');
        $client->rmdir('/traefik/frontends/pulsar-'.$instance->id, true);

        $client->rm('/traefik/backends/vfs-'.$instance->id.'/servers/server1/url');
        $client->rmdir('/traefik/backends/vfs-'.$instance->id, true);
        $client->rm('/traefik/frontends/vfs-'.$instance->id.'/routes/test_1/rule');
        $client->rm('/traefik/frontends/vfs-'.$instance->id.'/backend');
        $client->rmdir('/traefik/frontends/vfs-'.$instance->id, true);


        $instance->delete();

        return ['result' => 'success'];
    }

    private function getPrivateIp($rancher_container_id)
    {
        $client = new \GuzzleHttp\Client();
        $url = config('services.rancher.endpoint') . '/projects/1a5/containers/' . $rancher_container_id;
        $res = $client->request('GET', $url, [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
        ]);

        $payload = \GuzzleHttp\json_decode($res->getBody());
        if (!$payload->primaryIpAddress) {
            return null;
        }

        return $payload->primaryIpAddress;
    }

    private function getPulsarPort($rancher_container_id)
    {
        $client = new \GuzzleHttp\Client();
        $url = config('services.rancher.endpoint') . '/projects/1a5/containers/' . $rancher_container_id . '/ports';
        $res = $client->request('GET', $url, [
            'auth' => [config('services.rancher.user'), config('services.rancher.pass')],
        ]);

        $payload = \GuzzleHttp\json_decode($res->getBody());
        if (count($payload->data) === 0 || !$payload->data[0]->publicPort) {
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

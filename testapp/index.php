<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


$app = new Silex\Application();

$app->match(
    'echo',
    function (Request $req) {

        $psr7Factory = new DiactorosFactory();
        $request = $psr7Factory->createRequest($req);
        $ret = [
            'warning' => 'Do not expose this service in production : it is intrinsically unsafe',
        ];

        $ret['method'] = $request->getMethod();

        // Forms should be read from request, other data straight from input.
        if (!empty($requestData)) {
            foreach ($requestData as $key => $value) {
                $ret[$key] = $value;
            }
        }

        /** @var string $content */
        $content = $request->getBody()->getContents();
        if (!empty($content)) {
            $data = json_decode($content, true);
            if (!is_array($data)) {
                $ret['content'] = $content;
            } else {
                foreach ($data as $key => $value) {
                    $ret[$key] = $value;
                }
            }
        }

        $ret['headers'] = [];
        foreach ($request->headers->all() as $k => $v) {
            $ret['headers'][$k] = $v;
        }
        foreach ($request->query->all() as $k => $v) {
            $ret['query'][$k] = $v;
        }
        $response = new JsonResponse($ret);

        return $response;
    }
);

$app->run();

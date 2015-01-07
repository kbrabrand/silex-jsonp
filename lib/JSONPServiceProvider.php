<?php

namespace KBrabrand\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class JSONPServiceProvider implements ServiceProviderInterface {
    public function register(Application $app) {

        $app['JSONP.callback'] = 'callback';

        $app['JSONP.contentTypes'] = array(
            'application/json',
            'application/json; charset=utf-8',
            'application/javascript',
        );

        $app['JSONP'] = $app->protect(function (Request $req, Response $res) use ($app) {
            $callback = $req->get($app['JSONP.callback']);

            if ($callback !== null && $req->getMethod() === 'GET') {
                $contentType = $res->headers->get('Content-Type');

                // If the content type doesn't match, just quit
                if (!in_array($contentType, $app['JSONP.contentTypes'])) {
                    // Don't touch the response
                    return;
                }

                if ($res instanceof JsonResponse) {
                    $res->setCallBack($callback);
                } else {
                    $res->setContent(sprintf('%s(%s);', $callback, $res->getContent()));
                }
            }
        });
    }

    public function boot(Application $app) {
        $app->after($app['JSONP']);
    }
}
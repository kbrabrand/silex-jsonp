<?php

namespace KBrabrand\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class JSONPServiceProvider implements ServiceProviderInterface {
	// Allowed JSONP content types
	private $jsonpContentTypes = array(
        'application/json',
        'application/json; charset=utf-8',
        'application/javascript',
    );

	public function register(Application $app) {}

	public function boot(Application $app) {
		var_dump($app['jsonp_callback']);

		$app->after(function (Request $req, Response $res) {
            $callback = $req->get('callback');

            if ($callback !== null && $req->getMethod() === 'GET') {
                $contentType = $res->headers->get('Content-Type');

                // If the content type doesn't match, just quit
                if (!in_array($contentType, $this->jsonpContentTypes)) {
                    // Don't touch the response
                    return;
                }

                if ($res instanceof JsonResponse) {
                    $res->setCallBack($callback);
                } else {
                    $res->setContent($callback . '(' . $res->getContent() . ');');
                }
            }
        });
	}
}
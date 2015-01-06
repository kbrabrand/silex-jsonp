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

	/**
	 * Get accepted JSONP content types
	 *
	 * @return string[] Content types
	 */
	private function getJSONPContentTypes(Application $app) {
		if (isset($app['JSONP.contentTypes'])) {
			return $app['JSONP.contentTypes'];
		}

		return $this->jsonpContentTypes;
	}

	/**
	 * Get JSONP callback param name
	 *
	 * @return string Callback param name
	 */
	private function getJSONPCallbackParam(Application $app) {
		if (isset($app['JSONP.callback'])) {
			return $app['JSONP.callback'];
		}

		return 'callback';
	}

	public function register(Application $app) {
		$app['JSONP'] = $app->protect(function (Request $req, Response $res) use ($app) {
            $callback = $req->get($this->getJSONPCallbackParam($app));

            if ($callback !== null && $req->getMethod() === 'GET') {
                $contentType = $res->headers->get('Content-Type');

                // If the content type doesn't match, just quit
                if (!in_array($contentType, $this->getJSONPContentTypes($app))) {
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
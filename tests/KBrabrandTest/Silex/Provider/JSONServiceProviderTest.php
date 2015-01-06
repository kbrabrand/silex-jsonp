<?php

namespace KBrabrandTest\Silex\Provider;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KBrabrand\Silex\Provider\JSONPServiceProvider;

class JSONPServiceProviderTest extends \PHPUnit_Framework_TestCase {
    public function testProviderRegistered() {
        // Bootstrap the application
        $app = new Application();
        $app->register(new JSONPServiceProvider());

        $app->get('/', function() use ($app) {
            return $app->json(array(1, 2, 3));
        });

        $app->handle(Request::create('/'));

        // Assert that a after-hook (closure) is set for $app['JSONP']
        $this->assertTrue($app['JSONP'] instanceof \Closure);

    }

    public function testNonJSONPResponse() {
        // Bootstrap the application
        $app = new Application();
        $app->register(new JSONPServiceProvider());

        // Prepare the response
        $responseContent = 'foobar';
        $response = new Response($responseContent);

        // Set up route
        $app->get('/', function() use($app, $response) {
            return $response;
        });

        // Prepare request
        $request  = Request::create('/');
        $appResponse = $app->handle($request);

        // Assert that the response is not touched
        $this->assertEquals($appResponse->getContent(), $responseContent);
    }

    public function testDefaults() {
        // Bootstrap the application
        $app = new Application();
        $app->register(new JSONPServiceProvider());

        // Prepare response
        $responseContent = 'foobar';
        $response = new Response($responseContent);
        $response->headers->set('content-type', 'application/json');

        // Set up route
        $app->get('/', function() use($app, $response) {
            return $response;
        });

        // Prepare request
        $callback = 'myCallback';
        $request  = Request::create('/?callback=' . $callback);

        // Process request
        $appResponse = $app->handle($request);

        // Assert that the response is wrapped in the callback function
        $this->assertEquals(sprintf('%s(%s);', $callback, $responseContent), $appResponse->getContent());
    }

    public function testCustomSettings() {
        // Prepare the custom settings
        $customCallbackParam = 'c';
        $customContentTypes  = array(
            'foobar/barfoo'
        );

        // Bootstrap the application
        $app = new Application();
        $app->register(new JSONPServiceProvider(), array(
            'JSONP.contentTypes' => $customContentTypes,
            'JSONP.callback'     => $customCallbackParam
        ));

        // Prepare response with custom content type
        $responseContent = 'foobar';
        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $customContentTypes[0]);

        // Set up route
        $app->get('/', function() use($app, $response) {
            return $response;
        });

        // Prepare request
        $callback = 'myCallback';
        $request  = Request::create('/?' . $customCallbackParam . '=' . $callback);

        // Process request
        $appResponse = $app->handle($request);

        // Assert that the response is wrapped in the callback function
        $this->assertEquals($appResponse->getContent(), sprintf('%s(%s);', $callback, $responseContent));
    }
}

<?php

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\ZendView\ZendViewRenderer;
use Http\Client\Curl\Client;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzlePsr7Request;
use Zend\Diactoros\Request as DiactorosRequest;
use Zend\Diactoros\Uri as DiactorosURI;
use Psr\Http\Message\UriInterface;




class HomePageAction implements ServerMiddlewareInterface
{
    private $router;

    private $template;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null)
    {
        $this->router   = $router;
        $this->template = $template;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (! $this->template) {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the zend-expressive skeleton application.',
                'docsUrl' => 'https://docs.zendframework.com/zend-expressive/',
            ]);
        }



        //$client = new Client(new DiactorosMessageFactory(), new DiactorosStreamFactory());


        $config = [

            'headers' => [
                'Accept'        => 'application/ld+json'],
                'query' => ["q" => "http://data.swissbib.ch/person/2ca568b8-3e85-3c44-9a5e-aa3cbb7a190c",
                            "fields" => "@id"

                ]
        ];

        //["application/ld+json"]
        //["application/rdf+xml"]
        //["text/turtle"]
        //["application/n-triples"]
        //["text/html"]



        //'Accept'        => 'application/rdf+xml'
        $guzzleClient = new GuzzleClient($config);


        $antwort = $guzzleClient->get('http://data.swissbib.ch/person')->getBody()->getContents();



        $adapter = new GuzzleAdapter($guzzleClient);
        //$request = new Request('GET', 'http://data.swissbib.ch');
        //$request = new Request('GET', 'http://data.swissbib.ch/document');

        $u = new DiactorosURI("");


        //wie gehe ich damit um??
        //new GuzzlePsr7Request()


        $request = (new DiactorosRequest())
            ->withUri(new DiactorosURI('http://data.swissbib.ch/person'))
            ->withMethod('GET');
        //    ->withAddedHeader('Content-Type', 'application/rdf+xml');



        // Returns a Psr\Http\Message\ResponseInterface
        $response = $adapter->sendRequest($request);

        $test = $response->getBody()->getContents();



        $data['data'] = $antwort;

        /*
        if ($this->router instanceof Router\AuraRouter) {
            $data['routerName'] = 'Aura.Router';
            $data['routerDocs'] = 'http://auraphp.com/packages/2.x/Router.html';
        } elseif ($this->router instanceof Router\FastRouteRouter) {
            $data['routerName'] = 'FastRoute';
            $data['routerDocs'] = 'https://github.com/nikic/FastRoute';
        } elseif ($this->router instanceof Router\ZendRouter) {
            $data['routerName'] = 'Zend Router';
            $data['routerDocs'] = 'https://docs.zendframework.com/zend-router/';
        }

        if ($this->template instanceof PlatesRenderer) {
            $data['templateName'] = 'Plates';
            $data['templateDocs'] = 'http://platesphp.com/';
        } elseif ($this->template instanceof TwigRenderer) {
            $data['templateName'] = 'Twig';
            $data['templateDocs'] = 'http://twig.sensiolabs.org/documentation';
        } elseif ($this->template instanceof ZendViewRenderer) {
            $data['templateName'] = 'Zend View';
            $data['templateDocs'] = 'https://docs.zendframework.com/zend-view/';
        }
        */

        return new HtmlResponse($this->template->render('app::data', $data));
    }
}

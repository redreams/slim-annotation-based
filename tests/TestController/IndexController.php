<?php

namespace Redreams\Slim\Tests\TestController;

use Redreams\Slim\Annotation\Route;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * @Route("/")
 */
class IndexController
{
    /**
     * @Route("/")
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function indexAction(Request $request, Response $response): Response
    {
        return $response->write('index:index');
    }

    /**
     * @Route("/test")
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function testAction(Request $request, Response $response): Response
    {
        return $response->write('index:test');
    }

    /**
     * @Route("/getonly", methods={"GET"})
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function getOnlyAction(Request $request, Response $response): Response
    {
        return $response->write('index:getOnly');
    }

    /**
     * @Route("/named", name="namedAction")
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function namedAction(Request $request, Response $response): Response
    {
        return $response->write('index:named');
    }
}

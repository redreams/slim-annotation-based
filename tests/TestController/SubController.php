<?php

namespace Redreams\Slim\Tests\TestController;

use Redreams\Slim\Annotation\Route;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @Route("/sub")
 */
class SubController
{
    /**
     * @Route("/test")
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function testAction(Request $request, Response $response): Response
    {
        return $response->write('sub:test');
    }
}

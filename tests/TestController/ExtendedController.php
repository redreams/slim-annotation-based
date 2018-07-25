<?php

namespace Redreams\Slim\Tests\TestController;

use function get_class;
use Redreams\Slim\AbstractController;
use Redreams\Slim\Annotation\Route;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @Route("/ext")
 */
class ExtendedController extends AbstractController
{
    /**
     * @Route("/")
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function testAction(Request $request, Response $response): Response
    {
        return $response->write(get_class($this->container));
    }
}

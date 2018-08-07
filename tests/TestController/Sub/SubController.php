<?php

declare(strict_types=1);

/*
 * This file is part of the slim-annotation-based package.
 *
 * (c) Gennady Knyazkin <dev@gennadyx.tech>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Redreams\Slim\Tests\TestController\Sub;

use Redreams\Slim\AbstractController;
use Redreams\Slim\Annotation\Route;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @Route("/sub")
 */
class SubController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function indexAction(Request $request, Response $response): Response
    {
        return $response->write('sub:index');
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
        return $response->write('sub:test');
    }
}

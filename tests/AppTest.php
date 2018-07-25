<?php

/*
 * This file is part of the slim-annotation-based package.
 *
 * (c) Gennady Knyazkin <dev@gennadyx.tech>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Redreams\Slim\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use PHPUnit\Framework\TestCase;
use Redreams\Slim\App;
use Redreams\Slim\Exception\InvalidArgumentException;
use ReflectionClass;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Router;
use function http_build_query;

class AppTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Controllers directory "/invalidDirectory" does not exists
     */
    public function testInvalidControllersDirectory()
    {
        new App('/invalidDirectory');
    }

    public function testAnnotationReaderInstance()
    {
        $appReflection = new ReflectionClass(App::class);
        $readerProperty = $appReflection->getProperty('reader');
        $readerProperty->setAccessible(true);
        $this->assertInstanceOf(
            AnnotationReader::class,
            $readerProperty->getValue(new App(__DIR__.'/TestController'))
        );
        $this->assertInstanceOf(
            CachedReader::class,
            $readerProperty->getValue(new App(__DIR__.'/TestController',
                ['settings' => ['routerCacheDir' => __DIR__.'/cache']]))
        );
    }

    public function testControllersLoading()
    {
        $map = [
            '/'         => 'index:index',
            '/test'     => 'index:test',
            '/sub/test' => 'sub:test'
        ];
        foreach ($map as $k => $v) {
            $resp = $this->request('GET', $k);
            $this->assertEquals(200, $resp->getStatusCode());
            $this->assertEquals($v, $resp->getBody());
        }
    }

    public function testRouteMethods()
    {
        $resp = $this->request('POST', '/getonly');
        $this->assertEquals(405, $resp->getStatusCode());
    }

    public function testNamedRoute()
    {
        $app = new App(__DIR__.'/TestController');
        /** @var Router $router */
        $router = $app->getContainer()['router'];
        $namedRoute = $router->getNamedRoute('namedAction');
        $this->assertEquals('namedAction', $namedRoute->getName());
        $this->assertEquals('/named', $namedRoute->getPattern());
    }

    public function testControllerExtendedAbstractController()
    {
        $resp = $this->request('GET', '/ext');
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals(Container::class, $resp->getBody());
    }

    protected function request(string $method, string $path = '/', array $query = [], string $body = ''): Response
    {
        $app = new App(__DIR__.'/TestController');
        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $path.(!empty($query) ? '?'.http_build_query($query) : ''),
        ]);
        $req = Request::createFromEnvironment($env);
        if (!empty($body)) {
            $reqBody = new RequestBody();
            $reqBody->write($body);
            $req = $req->withBody($reqBody);
        }
        $app->getContainer()['request'] = $req;

        return $app->run(true);
    }
}

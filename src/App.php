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

namespace Redreams\Slim;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\FilesystemCache;
use FilesystemIterator;
use Generator;
use Psr\Container\ContainerInterface;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Redreams\ClassFinder\ClassFinder;
use Redreams\Slim\Annotation\Route;
use Redreams\Slim\Exception\InvalidArgumentException;
use ReflectionClass;
use ReflectionParameter;
use Slim\App as SlimApp;
use Slim\Interfaces\RouteInterface;
use Slim\Router;
use SplFileInfo;
use function file_get_contents;
use function is_dir;
use function rtrim;
use function sprintf;
use function trim;

/**
 * Class App
 *
 * @author Gennady Knyazkin <dev@gennadyx.tech>
 */
class App extends SlimApp
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * App constructor.
     *
     * @param string                   $controllersDir
     * @param array|ContainerInterface $container
     *
     * @throws InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function __construct(string $controllersDir, $container = [])
    {
        parent::__construct($container);
        if (!is_dir($controllersDir)) {
            throw new InvalidArgumentException(
                sprintf('Controllers directory "%s" does not exists', $controllersDir)
            );
        }
        AnnotationRegistry::registerLoader('class_exists');
        $this->reader = $this->createAnnotationReader();
        $this->loadControllers($controllersDir);
    }


    /**
     * @param string $controllerDir
     *
     * @return void
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function loadControllers(string $controllerDir)
    {
        /** @var SplFileInfo $file */
        foreach ($this->getControllerFiles($controllerDir) as $file) {
            if (($class = ClassFinder::find(file_get_contents($file->getRealPath()))) !== null) {
                $this->addRoutes($class);
            }
            gc_mem_caches();
        }
    }

    /**
     * @param string $class
     *
     * @return void
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    protected function addRoutes(string $class)
    {
        static $instance;
        /** @var Router $router */
        $router = $this->getContainer()->get('router');
        $settings = $this->getContainer()->get('settings');
        $relectionClass = new ReflectionClass($class);
        /** @var Route $classRoute */
        $classRoute = $this->reader->getClassAnnotation($relectionClass, Route::class);
        $pattern = '/';
        if ($classRoute !== null && !empty(trim($classRoute->getPattern(), '/'))) {
            $pattern = sprintf('/%s/', trim($classRoute->getPattern(), '/'));
        }
        foreach ($relectionClass->getMethods() as $reflectionMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);
            /** @var Route $methodRoute */
            if ($reflectionMethod->isStatic()
                || !$reflectionMethod->isPublic()
                || ($methodRoute = $this->getAnnotation($methodAnnotations, Route::class)) === null
            ) {
                continue;
            }
            if ($instance === null) {
                $instance = $this->createControllerInstance($relectionClass);
            }
            $methods = $methodRoute->getMethods() ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
            $route = $router->map(
                $methods,
                rtrim($pattern.ltrim($methodRoute->getPattern(), '/'), '/') ?: '/',
                [$instance, $reflectionMethod->getName()]
            );
            $route->setOutputBuffering($settings['outputBuffering']);
            if ($methodRoute->getName() !== null) {
                $route->setName($methodRoute->getName());
            }
            $this->handleActionAnnotations($methodAnnotations, $route);

        }
        if ($instance !== null) {
            $instance = null;
        }
    }

    /**
     * @param array  $annotations
     * @param string $name
     *
     * @return object|null
     */
    protected function getAnnotation(array $annotations, string $name)
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof $name) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param string $controllerDir
     *
     * @return Generator|SplFileInfo
     */
    protected function getControllerFiles(string $controllerDir)
    {
        $directoryIterator = new RecursiveDirectoryIterator(
            $controllerDir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
        );
        $iterator = new RecursiveCallbackFilterIterator($directoryIterator, function (SplFileInfo $file) {
            return $file->isDir() || ($file->isFile() && $file->getExtension() === 'php');
        });
        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            yield $file;
        }
    }

    /**
     * @param ReflectionClass $relectionClass
     *
     * @return object
     */
    protected function createControllerInstance(ReflectionClass $relectionClass)
    {
        $args = [];
        $constructor = $relectionClass->getConstructor();
        if ($constructor !== null && $constructor->getNumberOfParameters() > 0) {
            /** @var ReflectionParameter $firstParameter */
            $firstParameter = $constructor->getParameters()[0];
            $parameterClass = $firstParameter->getClass();
            if ($parameterClass !== null
                && (($parameterClass->isInterface() && $parameterClass->getName() === ContainerInterface::class)
                    || $parameterClass->implementsInterface(ContainerInterface::class))
            ) {
                $args[] = $this->getContainer();
            }
        }

        return $relectionClass->newInstanceArgs($args);
    }

    /**
     * @return Reader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \InvalidArgumentException
     */
    protected function createAnnotationReader(): Reader
    {
        $settings = $this->getContainer()->get('settings');
        if (isset($settings['routerCacheDir'])) {
            return new CachedReader(
                new AnnotationReader(),
                new FilesystemCache($settings['routerCacheDir']),
                false
            );
        }

        return new AnnotationReader();
    }

    /**
     * @param array          $methodAnnotations
     * @param RouteInterface $route
     *
     * @return void
     */
    protected function handleActionAnnotations(array $methodAnnotations, RouteInterface $route)
    {
    }
}

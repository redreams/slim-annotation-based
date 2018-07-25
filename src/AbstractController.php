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

use Slim\Container;

/**
 * Class AbstractController
 *
 * @author Gennady Knyazkin <dev@gennadyx.tech>
 */
abstract class AbstractController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * AbstractController constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Slim\Exception\ContainerValueNotFoundException
     */
    protected function get(string $name)
    {
        return $this->container->get($name);
    }
}

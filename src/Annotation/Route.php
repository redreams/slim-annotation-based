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

namespace Redreams\Slim\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Redreams\Slim\Exception\InvalidArgumentException;
use function in_array;
use function sprintf;

/**
 * Class Route
 *
 * @Annotation
 * @Annotation\Target({"CLASS", "METHOD"})
 * @Annotation\Attributes({
 *     @Annotation\Attribute("methods", type="array"),
 *     @Annotation\Attribute("name", type="string")
 * })
 * @author Gennady Knyazkin <dev@gennadyx.tech>
 */
final class Route
{
    const AVAILABLE_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array|null
     */
    private $methods;

    /**
     * @var string|null
     */
    private $name;

    /**
     * Route constructor.
     *
     * @param array $values
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $values)
    {
        $pattern = $values['value'] ?? '';
        if (empty($pattern)) {
            throw new InvalidArgumentException('Pattern required.');
        }
        $this->pattern = $pattern;
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['methods'])) {
            $methods = (array)$values['methods'];
            foreach ($methods as $method) {
                if (!in_array($method, self::AVAILABLE_METHODS, true)) {
                    throw new InvalidArgumentException(
                        sprintf('Invalid method "%s". Available methods: [%s]', $method, self::AVAILABLE_METHODS)
                    );
                }
            }
            $this->methods = $methods;
        }
    }

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get methods
     *
     * @return array|null
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }
}

# slim-annotation-based

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

[![Coverage Status][ico-coverage]][link-coverage]
[![Sensiolabs_Medal][ico-code-quality-sensio]][link-code-quality-sensio]
[![Quality Score][ico-code-quality-scrutinizer]][link-code-quality-scrutinizer]

slim-annotation-based composer package

## Install

Via Composer

``` bash
$ composer require redreams/slim-annotation-based
```

## Usage

``` php

use Redreams\Slim\App;
//front controller (index.php)
$config = [];//defaul slim config
$config['settings']['routerCacheDir'] = './dirForDoctrineAnnotationReader';//optional
$app = new App('./controllersDir', $config);

// controllersDir/IndexController.php
use Redreams\Slim\AbstractController;
use Redreams\Slim\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
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

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email dev@gennadyx.tech instead of using the issue tracker.

## Credits

- [Gennady Knyazkin][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/redreams/slim-annotation-based.svg?style=flat
[ico-license]: https://img.shields.io/packagist/l/redreams/slim-annotation-based.svg
[ico-coverage]: https://img.shields.io/scrutinizer/coverage/g/redreams/slim-annotation-based.svg?style=flat
[ico-code-quality-scrutinizer]: https://img.shields.io/scrutinizer/g/redreams/slim-annotation-based.svg?style=flat
[ico-code-quality-sensio]: https://insight.sensiolabs.com/projects/2f7fd89f-1300-4cd8-8347-8817e52583fb/mini.png
[ico-downloads]: https://img.shields.io/packagist/dt/redreams/slim-annotation-based.svg?style=flat

[link-packagist]: https://packagist.org/packages/redreams/slim-annotation-based
[link-coverage]: https://scrutinizer-ci.com/g/redreams/slim-annotation-based/code-structure
[link-code-quality-scrutinizer]: https://scrutinizer-ci.com/g/redreams/slim-annotation-based
[link-code-quality-sensio]: https://insight.sensiolabs.com/projects/2f7fd89f-1300-4cd8-8347-8817e52583fb
[link-downloads]: https://packagist.org/packages/redreams/slim-annotation-based
[link-author]: http://gennadyx.tech
[link-contributors]: https://github.com/redreams/slim-annotation-based/contributors

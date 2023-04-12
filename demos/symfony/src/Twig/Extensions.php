<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extensions extends AbstractExtension
{
    public function __construct(
        private ParameterBagInterface $params,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('isMobile', [$this, 'isMobile']),
            new TwigFunction('getBrowser', [$this, 'getBrowser']),
            new TwigFunction('getPlatform', [$this, 'getPlatform']),
            new TwigFunction('getParameter', [$this, 'getParameter']),
            new TwigFunction('isValidResourceForLightbox', [$this, 'isValidResourceForLightbox']),
            new TwigFunction('chr', [$this, 'chr']),
            new TwigFunction('getTheFirstCharacter', [$this, 'getTheFirstCharacter']),

            new TwigFunction(
                'methodExist',
                function (Environment $twig, $name) {
                    return $twig->getFunction($name) !== null;
                },
                ['needs_environment' => true],
            ),
            new TwigFunction('instanceOf', [$this, 'instanceOf']),
        ];
    }

    public function instanceOf($a, $b)
    {
        return $a instanceof $b;
    }

    public function getTheFirstCharacter(string $string): string
    {
        return substr($string, 0, 1);
    }

    public function isMobile($user_agent)
    {
        if (
            strpos($user_agent, 'Android') !== false ||
            strpos($user_agent, 'IEMobile') !== false ||
            strpos($user_agent, 'BlackBerry') !== false ||
            strpos($user_agent, 'iPod') !== false ||
            strpos($user_agent, 'iPad') !== false ||
            strpos($user_agent, 'iPhone') !== false ||
            strpos($user_agent, 'webOS') !== false
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getBrowser($user_agent)
    {
        if (strpos($user_agent, 'MSIE') !== false) {
            return 'ie';
        } elseif (strpos($user_agent, 'Edge') !== false) {
            //Microsoft Edge
            return 'edge';
        } elseif (strpos($user_agent, 'Trident') !== false) {
            //IE 11
            return 'ie';
        } elseif (strpos($user_agent, 'Opera Mini') !== false) {
            return 'op';
        } elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== false) {
            return 'op';
        } elseif (strpos($user_agent, 'Firefox') !== false) {
            return 'moz';
        } elseif (strpos($user_agent, 'Chrome') !== false) {
            return 'chrome';
        } elseif (strpos($user_agent, 'Safari') !== false) {
            return 'safari';
        } elseif (strpos($user_agent, 'Android') !== false) {
            return 'android';
        } elseif (strpos($user_agent, 'IEMobile') !== false) {
            return 'iemobile';
        } elseif (strpos($user_agent, 'BlackBerry') !== false) {
            return 'blackberry';
        } elseif (strpos($user_agent, 'iPod') !== false) {
            return 'ipod';
        } elseif (strpos($user_agent, 'iPad') !== false) {
            return 'ipad';
        } elseif (strpos($user_agent, 'iPhone') !== false) {
            return 'iphone';
        } elseif (strpos($user_agent, 'webOS') !== false) {
            return 'webos';
        } else {
            return 'No hemos podido detectar su navegador';
        }
    }

    public function getPlatform($user_agent): string
    {
        $plataformas = [
            'windows10' => 'Windows NT 10.0+',
            'windows8.1' => 'Windows NT 6.3+',
            'windows8' => 'Windows NT 6.2+',
            'windows7' => 'Windows NT 6.1+',
            'windowsVista' => 'Windows NT 6.0+',
            'windowsXP' => 'Windows NT 5.1+',
            'windows2003' => 'Windows NT 5.2+',
            'windows' => 'Windows otros',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
            'macOS-X' => '(Mac OS X+)|(CFNetwork+)',
            'mac-otros' => 'Macintosh',
            'android' => 'Android',
            'blackBerry' => 'BlackBerry',
            'linux' => 'Linux',
        ];
        foreach ($plataformas as $plataforma => $pattern) {
            if (preg_match('/(?i)' . $pattern . '/', $user_agent)) {
                return $plataforma;
            }
        }
        return 'Otras';
    }

    public function getParameter($parameter)
    {
        return $this->params->get($parameter);
    }

    public function isValidResourceForLightbox($path_file)
    {
        $path_file_info = pathinfo($path_file);

        return in_array(strtolower($path_file_info['extension']), ['mp4', 'jpeg', 'jpg', 'png']) ? true : false;
    }

    public function chr(int $codepoint)
    {
        return chr($codepoint);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('int', function ($value) {
                return (int) $value;
            }),
            new TwigFilter('float', function ($value) {
                return (float) $value;
            }),
            new TwigFilter('string', function ($value) {
                return (string) $value;
            }),
            new TwigFilter('bool', function ($value) {
                return (bool) $value;
            }),
            new TwigFilter('array', function (object $value) {
                return (array) $value;
            }),
            new TwigFilter('object', function (array $value) {
                return (object) $value;
            }),
            new TwigFilter('formatToHtml', function (string $value) {
                $value = trim($value);
                $value = preg_replace(
                    '/(mailto:([\.\-\_a-zA-Z0-9]+@[\.\-\_a-zA-Z0-9]+\.[a-zA-Z]{1,63}))+/',
                    '<a href="mailto:$2">$2</a>',
                    $value,
                    -1,
                );
                $value = preg_replace('/\n/', '<br>', $value);

                return $value;
            }),
        ];
    }
}

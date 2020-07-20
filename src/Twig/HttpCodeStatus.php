<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HttpCodeStatus extends AbstractExtension
{

    public function getFunctions()
    {
        return [
            new TwigFunction('httpStatus', [$this, 'getHttpStatusCode']),
        ];
    }

    public function getHttpStatusCode()
    {
        return http_response_code();
    }
}

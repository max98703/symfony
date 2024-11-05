<?php

namespace Api\Utils\Jttp;

use Symfony\Component\HttpFoundation\Response;

class HttpUtils
{
    static function getStatusCode200(): int {
        return Response::HTTP_OK;
    }

    static function getHttpStatus($httpCode): string {
        return isset(Response::$statusTexts[$httpCode])?Response::$statusTexts[$httpCode]:'';
    }
}
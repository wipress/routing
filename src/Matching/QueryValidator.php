<?php

namespace WIPress\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Routing\Route;

class QueryValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    const KEY = '__wipress_route';

    /**
     * @param Route $route
     * @param Request $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        return (bool) preg_match(
            $route->getCompiled()->getRegex(), 
            sprintf('/%s', trim(rawurldecode($request->{self::KEY}), '/'))
        );
    }
}
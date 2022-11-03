<?php

namespace WIPress\Routing;

use WIPress\Routing\Matching\QueryValidator;
use Illuminate\Routing\RouteParameterBinder as BaseRouteParameterBinder;

class RouteParameterBinder extends BaseRouteParameterBinder
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function parameters($request)
    {
        return $this->replaceDefaults($this->bindQueryParameters($request));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function bindQueryParameters($request)
    {
        preg_match(
            $this->route->compiled->getRegex(), 
            sprintf('/%s', trim(rawurldecode($request->{QueryValidator::KEY}), '/')), 
            $matches
        );

        return $this->matchToKeys(array_slice($matches, 1));
    }
}
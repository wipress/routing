<?php

namespace WIPress\Routing;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\RouteUrlGenerator as BaseRouteUrlGenerator;
use WIPress\Routing\Matching\QueryValidator;

class RouteUrlGenerator extends BaseRouteUrlGenerator
{
    /**
     * Generate a URL for the given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function to($route, $parameters = [], $absolute = false)
    {
        if ($this->request->page) {
            $parameters = array_merge($parameters, ['page' => $this->request->page]);
        }

        $uri = rawurldecode($this->addQueryString($route, $parameters));

        if (preg_match_all('/{(.*?)}/', $uri, $matchedMissingParameters)) {
            throw UrlGenerationException::forMissingParameters($route, $matchedMissingParameters[1]);
        }

        $uri = strtr(rawurlencode($uri), $this->dontEncode);

        if (!$absolute) {
            $uri = preg_replace('#^(//|[^/?])+#', '', $uri);

            if ($base = $this->request->getBaseUrl()) {
                $uri = preg_replace('#^' . $base . '#i', '', $uri);
            }

            return '/' . ltrim($uri, '/');
        }

        return $uri;
    }

    /**
     * @param  Route  $route
     * @param  array  $parameters
     * @return mixed|string
     */
    protected function addQueryString($route, array $parameters)
    {
        $domain = $this->getRouteDomain($route, $parameters);

        $uri = $this->url->format(
            $this->replaceRootParameters($route, $domain, $parameters),
            '',
            $route
        );

        if (!is_null($fragment = parse_url($uri, PHP_URL_FRAGMENT))) {
            $uri = preg_replace('/#.*/', '', $uri);
        }

        $uri .= $this->getRouteQueryString(array_merge(
            [
                QueryValidator::KEY => sprintf('/%s', trim($this->replaceRouteParameters($route->uri(), $parameters))),
            ],
            $parameters
        ));

        return is_null($fragment) ? $uri : $uri . "#{$fragment}";
    }
}

<?php

namespace WIPress\Routing;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorInterface;
use Illuminate\Routing\RoutingServiceProvider as BaseServiceProvider;

abstract class RoutingServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }

    /**
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app->router->getRoutes();

            $app->instance('routes', $routes);

            return new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                ), $app->config->get('app.asset_url')
            );
        });

        $this->app->extend('url', function (UrlGeneratorInterface $url, $app) {
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                return $this->app->config->get('app.key');
            });

            $app->rebinding('routes', function ($app, $routes) {
                $app->url->setRoutes($routes);
            });

            return $url;
        });
    }


    /**
     *
     * @return Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app->url->setRequest($request);
        };
    }
}

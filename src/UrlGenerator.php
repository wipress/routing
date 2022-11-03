<?php

namespace WIPress\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @param string $scheme
     * @param null|string $root
     */
    public function formatRoot($scheme, $root = null)
    {
        return parent::formatRoot($scheme, home_url());
    }

    /**
     * @return RouteUrlGenerator
     */
    protected function routeUrl()
    {
        if (!$this->routeGenerator) {
            $this->routeGenerator = new RouteUrlGenerator($this, $this->request);
        }

        return $this->routeGenerator;
    }
}

<?php

namespace Illuminate\Support;

if (! function_exists('Illuminate\Support\extension_loaded')) {
    /**
     * Polyfill for intl extension check inside the Illuminate\Support namespace.
     */
    function extension_loaded(string $extension): bool
    {
        if ($extension === 'intl') {
            return class_exists('NumberFormatter');
        }

        return \extension_loaded($extension);
    }
}

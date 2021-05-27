<?php

if( !function_exists('getLanguagePrefix') ) {
    function getLanguagePrefix()
    {
        if (app()->runningInConsole()) {
            return '/';
        }

        if (Request()->segment(1) === 'api') {
            return Request()->segment(2);
        }

        return Request()->segment(1);
    }
}

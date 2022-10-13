<?php

namespace Core;

/**
 * Middlewares interface.
 */
interface Middleware
{

    /**
     * Handle method.
     *
     * @return void
     */
    public static function handle();
}
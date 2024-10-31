<?php

class Quform_WPML
{
    /**
     * Register services with the container
     *
     * @param Quform_Container $container
     */
    public static function containerSetup(Quform_Container $container)
    {
        new Quform_WPML_Container($container);
    }

    /**
     * Bootstrap the plugin
     *
     * @param Quform_Container $container
     */
    public static function bootstrap(Quform_Container $container)
    {
        new Quform_WPML_Dispatcher($container);
    }
}

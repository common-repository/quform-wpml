<?php

class Quform_WPML_ClassLoader
{
    /**
     * Register this class loader with the spl autoload queue
     */
    public static function register()
    {
        spl_autoload_register(array('Quform_WPML_ClassLoader', 'loadClass'));
    }

    /**
     * Attempt to load the given class
     *
     * @param   string  $class  The class name
     * @return  bool            True if the class was found
     */
    public static function loadClass($class)
    {
        // Don't interfere with other autoloaders
        if (strpos($class, 'Quform_WPML') !== 0) {
            return false;
        }

        $directory = dirname(dirname(dirname(__FILE__)));
        $class = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        if (file_exists($path = $directory . DIRECTORY_SEPARATOR . $class)) {
            require_once $path;
            return true;
        }
    }
}

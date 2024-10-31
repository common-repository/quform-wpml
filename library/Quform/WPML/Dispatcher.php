<?php

class Quform_WPML_Dispatcher
{
    /**
     * @param Quform_Container $container
     */
    public function __construct(Quform_Container $container)
    {
        if (defined('QUFORM_VERSION') && version_compare(QUFORM_VERSION, '2.16.0', '<')) {
            add_action('admin_notices', array($this, 'showUpdateQuformNotice'));
            return;
        }

        if ( ! defined('ICL_SITEPRESS_VERSION')) {
            return;
        } elseif (version_compare(ICL_SITEPRESS_VERSION, '3.2', '<')) {
            add_action('admin_notices', array($this, 'showUpdateWPMLNotice'));
            return;
        }

        if ( ! defined('WPML_ST_VERSION')) {
            add_action('admin_notices', array($this, 'showInstallStringTranslationNotice'));
            return;
        }

        add_filter('quform_locale', array($container['wpmlLocaleTranslator'], 'setLocale'));
        add_filter('quform_active_locales', array($container['wpmlLocaleTranslator'], 'addActiveLocales'));
        add_filter('quform_form_factory_pre_create', array($container['wpmlFormTranslator'], 'translateForm'));

        if (is_admin() || defined('QUFORM_TESTING')) {
            add_action('quform_add_form', array($container['wpmlFormTranslator'], 'registerStrings'));
            add_action('quform_save_form', array($container['wpmlFormTranslator'], 'registerStrings'));
            add_action('quform_form_deleted', array($container['wpmlFormTranslator'], 'deletePackage'));
        }
    }

    /**
     * Show an admin notice if the Quform plugin is not compatible with this add-on
     */
    public function showUpdateQuformNotice()
    {
        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html__('Please update the Quform plugin to version 2.16.0 or later to use the Quform WPML add-on.', 'quform-wpml')
        );
    }

    /**
     * Show an admin notice if the WPML plugin is not compatible with this add-on
     */
    public function showUpdateWPMLNotice()
    {
        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html__('Please update the WPML plugin to version 3.2 or later to use the Quform WPML add-on.', 'quform-wpml')
        );
    }

    /**
     * Show an admin notice if the WPML String Translation plugin is not installed
     */
    public function showInstallStringTranslationNotice()
    {
        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html__('Please install the WPML String Translation plugin to use the Quform WPML add-on.', 'quform-wpml')
        );
    }
}

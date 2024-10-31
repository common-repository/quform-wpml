<?php

class Quform_WPML_LocaleTranslator
{
    /**
     * Set the global locale to the WPML locale
     *
     * @param   string  $locale
     * @return  string
     */
    public function setLocale($locale)
    {
        global $wpdb;
        $active_language = apply_filters('wpml_current_language', null);

        if (Quform::isNonEmptyString($active_language)) {
            $result = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT default_locale FROM {$wpdb->prefix}icl_languages WHERE code = %s LIMIT 1",
                    $active_language
                )
            );

            if (Quform::isNonEmptyString($result)) {
                $result = str_replace('_', '-', $result);
                $availableLocales = Quform::getLocales();

                if (array_key_exists($result, $availableLocales)) {
                    $locale = $result;
                }
            }
        }

        return $locale;
    }

    /**
     * Add any active WPML locales as active locales
     *
     * @param   array  $locales
     * @return  array
     */
    public function addActiveLocales($locales)
    {
        global $wpdb;
        $availableLocales = Quform::getLocales();

        $results = $wpdb->get_col("SELECT default_locale FROM {$wpdb->prefix}icl_languages WHERE active = 1");

        foreach ($results as $result) {
            if (Quform::isNonEmptyString($result)) {
                $locale = str_replace('_', '-', $result);

                if (array_key_exists($locale, $availableLocales)) {
                    $locales[] = $locale;
                }
            }
        }

        return $locales;
    }
}

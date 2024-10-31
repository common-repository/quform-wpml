<?php

class Quform_WPML_Container
{
    public function __construct(Quform_Container $container)
    {
        $container['wpmlFormTranslator'] = new JuiceDefinition('Quform_WPML_FormTranslator');
        $container['wpmlLocaleTranslator'] = new JuiceDefinition('Quform_WPML_LocaleTranslator');
    }
}

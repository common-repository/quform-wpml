<?php

class Quform_WPML_FormTranslator
{
    /**
     * Get the package configuration
     *
     * @param   int     $formId    The form ID
     * @param   string  $formName  The form name
     * @return  array
     */
    protected function getPackage($formId, $formName)
    {
        return array(
            'kind' => 'Quform Form',
            'name' => $formId,
            'title' => $formName,
            'edit_link' => sprintf(admin_url('admin.php?page=quform.forms&sp=edit&id=%d'), $formId),
            'view_link' => '',
        );
    }

    /**
     * Register strings for a form
     *
     * @param array $config The form configuration
     */
    public function registerStrings(array $config)
    {
        $formId = Quform::get($config, 'id', 0);
        $formName = Quform::get($config, 'name', '');
        $package = $this->getPackage($formId, $formName);

        do_action('wpml_register_string', Quform::get($config, 'title', ''), 'form_title', $package, 'Form title', 'LINE');
        do_action('wpml_register_string', Quform::get($config, 'description', ''), 'form_description', $package, 'Form description', 'AREA');

        if (Quform::get($config, 'limitEntries', false) && is_numeric($entryLimit = Quform::get($config, 'entryLimit', '')) && $entryLimit > 0) {
            do_action('wpml_register_string', Quform::get($config, 'entryLimitReachedMessage', ''), 'entry_limit_reached_message', $package, 'Entry limit reached message', 'VISUAL');
            do_action('wpml_register_string', Quform::get($config, 'thisFormIsCurrentlyClosed', ''), 'entry_limit_reached_error_message', $package, 'Entry limit reached error message', 'LINE');
        }

        do_action('wpml_register_string', Quform::get($config, 'requiredText', ''), 'required_text', $package, 'Required text', 'LINE');

        if (Quform::get($config, 'enableSchedule', false)) {
            if (Quform::isNonEmptyString(Quform::get($config, 'scheduleStart', ''))) {
                do_action('wpml_register_string', Quform::get($config, 'scheduleStartMessage', ''), 'schedule_start_message', $package, 'Schedule start message', 'VISUAL');
                do_action('wpml_register_string', Quform::get($config, 'formIsNotYetOpenForSubmissions', ''), 'schedule_start_error_message', $package, 'Schedule start error message', 'LINE');
            }

            if (Quform::isNonEmptyString(Quform::get($config, 'scheduleEnd', ''))) {
                do_action('wpml_register_string', Quform::get($config, 'scheduleEndMessage', ''), 'schedule_end_message', $package, 'Schedule end message', 'VISUAL');
                do_action('wpml_register_string', Quform::get($config, 'formIsNoLongerOpenForSubmissions', ''), 'schedule_end_error_message', $package, 'Schedule end error message', 'LINE');
            }
        }

        $submitType = Quform::get($config, 'submitType', 'default');

        if ($submitType == 'image') {
            do_action('wpml_register_string', Quform::get($config, 'submitImage', ''), 'submit_button_image', $package, 'Submit button image', 'LINE');
        } elseif ($submitType == 'html') {
            do_action('wpml_register_string', Quform::get($config, 'submitHtml', ''), 'submit_button_html', $package, 'Submit button HTML', 'VISUAL');
        } else {
            do_action('wpml_register_string', Quform::get($config, 'submitText', ''), 'submit_button_text', $package, 'Submit button text', 'LINE');
        }

        $elements = Quform::get($config, 'elements', array());

        if (is_array($elements)) {
            if (count($elements) > 1) {
                $nextType = Quform::get($config, 'nextType', 'default');

                if ($nextType == 'image') {
                    do_action('wpml_register_string', Quform::get($config, 'nextImage', ''), 'next_button_image', $package, 'Next button image', 'LINE');
                } elseif ($nextType == 'html') {
                    do_action('wpml_register_string', Quform::get($config, 'nextHtml', ''), 'next_button_html', $package, 'Next button HTML', 'VISUAL');
                } else {
                    do_action('wpml_register_string', Quform::get($config, 'nextText', ''), 'next_button_text', $package, 'Next button text', 'LINE');
                }

                $backType = Quform::get($config, 'backType', 'default');

                if ($backType == 'image') {
                    do_action('wpml_register_string', Quform::get($config, 'backImage', ''), 'back_button_image', $package, 'Back button image', 'LINE');
                } elseif ($backType == 'html') {
                    do_action('wpml_register_string', Quform::get($config, 'backHtml', ''), 'back_button_html', $package, 'Back button HTML', 'VISUAL');
                } else {
                    do_action('wpml_register_string', Quform::get($config, 'backText', ''), 'back_button_text', $package, 'Back button text', 'LINE');
                }

                if (Quform::get($config, 'pageProgressType', 'numbers') == 'numbers') {
                    do_action('wpml_register_string', Quform::get($config, 'pageProgressNumbersText', ''), 'page_progress_numbers_text', $package, 'Page progress numbers text', 'LINE');
                }
            }

            foreach ($elements as $page) {
                if (is_array($page)) {
                    $this->registerContainerStrings($page, $formId, $package);
                }
            }
        }

        $notifications = Quform::get($config, 'notifications', array());

        if (is_array($notifications)) {
            foreach ($notifications as $notification) {
                $notificationId = Quform::get($notification, 'id', 0);
                $name = "notification_{$formId}_{$notificationId}";

                do_action('wpml_register_string', Quform::get($notification, 'subject', ''), "{$name}_subject", $package, "Notification {$name} subject", 'LINE');

                if (in_array($notification['format'], array('html', 'multipart'), true)) {
                    do_action('wpml_register_string', Quform::get($notification, 'html', ''), "{$name}_html", $package, "Notification {$name} HTML", 'VISUAL');
                }

                if (in_array($notification['format'], array('text', 'multipart'), true)) {
                    do_action('wpml_register_string', Quform::get($notification, 'text', ''), "{$name}_text", $package, "Notification {$name} text", 'AREA');
                }
            }
        }

        $confirmations = Quform::get($config, 'confirmations', array());

        if (is_array($confirmations)) {
            foreach ($confirmations as $confirmation) {
                $confirmationId = Quform::get($confirmation, 'id', 0);
                $name = "confirmation_{$formId}_{$confirmationId}";

                if (in_array($confirmation['type'], array('message', 'message-redirect-page', 'message-redirect-url'), true)) {
                    do_action('wpml_register_string', Quform::get($confirmation, 'message', ''), "{$name}_message", $package, "Confirmation {$name} message", 'VISUAL');
                }
            }
        }

        do_action('wpml_register_string', Quform::get($config, 'messageRequired', ''), 'validator_required_message', $package, 'Required validator message', 'LINE');

        if (Quform::get($config, 'oneEntryPerUser', false)) {
            do_action('wpml_register_string', Quform::get($config, 'onlyOneSubmissionAllowed', ''), 'one_entry_per_user_error_message', $package, 'One entry per user error message', 'LINE');
        }
    }

    /**
     * Register the strings for a container
     *
     * @param  array  $container  The container configuration
     * @param  int    $formId     The form ID
     * @param  array  $package    The package configuration
     */
    protected function registerContainerStrings(array $container, $formId, array $package)
    {
        $this->registerElementStrings($container, $formId, $package);

        $elements = Quform::get($container, 'elements', array());

        if (is_array($elements)) {
            foreach($elements as $element) {
                if (is_array($element)) {
                    $type = Quform::get($element, 'type', '');

                    if (in_array($type, array('group', 'row', 'column'), true)) {
                        $this->registerContainerStrings($element, $formId, $package);
                    } else {
                        $this->registerElementStrings($element, $formId, $package);
                    }
                }
            }
        }
    }

    /**
     * Register the strings for an element
     *
     * @param  array  $element  The element configuration
     * @param  int    $formId   The form ID
     * @param  array  $package  The package configuration
     */
    protected function registerElementStrings(array $element, $formId, array $package)
    {
        $elementId = Quform::get($element, 'id', 0);
        $identifier = "{$formId}_{$elementId}";
        $name = "field_$identifier";
        $type = Quform::get($element, 'type', '');

        if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'html', 'captcha', 'recaptcha', 'submit', 'group', 'page'), true)) {
            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha', 'page'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'label', ''), "{$name}_label", $package, "Field $identifier label", 'LINE');
            }

            if (in_array($type, array('select', 'checkbox', 'radio', 'multiselect'), true)) {
                $options = Quform::get($element, 'options', array());

                if (is_array($options)) {
                    foreach ($options as $option) {
                        if (is_array($option)) {
                            $optionId = Quform::get($option, 'id', 0);
                            $optionLabel = Quform::get($option, 'label', '');
                            $optgroupOptions = Quform::get($option, 'options');

                            if (in_array($type, array('select', 'multiselect'), true) && is_array($optgroupOptions)) {
                                do_action('wpml_register_string', $optionLabel, "{$name}_optgroup_{$optionId}_label", $package, "Field $identifier optgroup $optionId label", 'LINE');

                                foreach ($optgroupOptions as $optgroupOption) {
                                    $optgroupOptionId = Quform::get($optgroupOption, 'id', 0);
                                    $optgroupOptionLabel = Quform::get($optgroupOption, 'label', '');

                                    do_action('wpml_register_string', $optgroupOptionLabel, "{$name}_option_{$optgroupOptionId}_label", $package, "Field $identifier option $optgroupOptionId label", 'LINE');
                                }
                            } else {
                                do_action('wpml_register_string', $optionLabel, "{$name}_option_{$optionId}_label", $package, "Field $identifier option $optionId label", 'LINE');
                            }
                        }
                    }
                }
            }

            if (in_array($type, array('group', 'page'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'title', ''), "{$name}_title", $package, "Field $identifier title", 'AREA');
                do_action('wpml_register_string', Quform::get($element, 'description', ''), "{$name}_description", $package, "Field $identifier description", 'AREA');
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'description', ''), "{$name}_description", $package, "Field $identifier description", 'AREA');
                do_action('wpml_register_string', Quform::get($element, 'descriptionAbove', ''), "{$name}_description_above", $package, "Field $identifier description above", 'AREA');
            }

            if ($type == 'name') {
                $anyNamePartRequired = false;

                if (Quform::get($element, 'prefixEnabled', false)) {
                    if (Quform::get($element, 'prefixRequired', false)) {
                        $anyNamePartRequired = true;
                    }

                    $prefixOptions = Quform::get($element, 'prefixOptions', array());

                    if (is_array($prefixOptions)) {
                        foreach ($prefixOptions as $prefixOption) {
                            if (is_array($prefixOption)) {
                                $prefixOptionId = Quform::get($prefixOption, 'id', 0);
                                $prefixOptionLabel = Quform::get($prefixOption, 'label', '');

                                do_action('wpml_register_string', $prefixOptionLabel, "{$name}_prefix_option_{$prefixOptionId}_label", $package, "Field $identifier prefix option $prefixOptionId label", 'LINE');
                            }
                        }
                    }

                    do_action('wpml_register_string', Quform::get($element, 'prefixSubLabel', ''), "{$name}_prefix_sub_label", $package, "Field $identifier prefix sub label", 'LINE');
                    do_action('wpml_register_string', Quform::get($element, 'prefixSubLabelAbove', ''), "{$name}_prefix_sub_label_above", $package, "Field $identifier prefix sub label above", 'LINE');

                    if (Quform::get($element, 'prefixNoneOption', true)) {
                        do_action('wpml_register_string', Quform::get($element, 'prefixNoneOptionText', ''), "{$name}_prefix_none_option_text", $package, "Field $identifier prefix none option text", 'LINE');
                    }
                }

                foreach (array('first', 'middle', 'last', 'suffix') as $part) {
                    if (Quform::get($element, "{$part}Enabled", false)) {
                        if (Quform::get($element, "{$part}Required", false)) {
                            $anyNamePartRequired = true;
                        }

                        do_action('wpml_register_string', Quform::get($element, "{$part}Placeholder", ''), "{$name}_{$part}_placeholder", $package, "Field $identifier $part placeholder", 'LINE');
                        do_action('wpml_register_string', Quform::get($element, "{$part}SubLabel", ''), "{$name}_{$part}_sub_label", $package, "Field $identifier $part sub label", 'LINE');
                        do_action('wpml_register_string', Quform::get($element, "{$part}SubLabelAbove", ''), "{$name}_{$part}_sub_label_above", $package, "Field $identifier $part sub label above", 'LINE');
                    }
                }

                if ($anyNamePartRequired) {
                    do_action('wpml_register_string', Quform::get($element, 'messageRequired', ''), "{$name}_validator_required_message", $package, "Field $identifier validator required message", 'LINE');
                }
            }

            if ($type == 'submit') {
                foreach (array('submit', 'next', 'back') as $which) {
                    $submitType = Quform::get($element, "{$which}Type", 'default');
                    $whichHuman = ucfirst($which);

                    if ($submitType == 'image') {
                        do_action('wpml_register_string', Quform::get($element, "{$which}Image", ''), "{$name}_{$which}_button_image", $package, "Field $identifier $whichHuman button image", 'LINE');
                    } elseif ($submitType == 'html') {
                        do_action('wpml_register_string', Quform::get($element, "{$which}Html", ''), "{$name}_{$which}_button_html", $package, "Field $identifier $whichHuman button HTML", 'VISUAL');
                    } else {
                        do_action('wpml_register_string', Quform::get($element, "{$which}Text", ''), "{$name}_{$which}_button_text", $package, "Field $identifier $whichHuman button text", 'LINE');
                    }
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'date', 'time', 'password', 'captcha'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'placeholder', ''), "{$name}_placeholder", $package, "Field $identifier placeholder", 'LINE');
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'subLabel', ''), "{$name}_sub_label", $package, "Field $identifier sub label", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'subLabelAbove', ''), "{$name}_sub_label_above", $package, "Field $identifier sub label above", 'LINE');

                if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password'), true)) {
                    do_action('wpml_register_string', Quform::get($element, 'adminLabel', ''), "{$name}_admin_label", $package, "Field $identifier admin label", 'LINE');
                }

                do_action('wpml_register_string', Quform::get($element, 'tooltip', ''), "{$name}_tooltip", $package, "Field $identifier tooltip", 'LINE');
            }

            if (in_array($type, array('text', 'textarea', 'email'), true)) {
                do_action('wpml_register_string', Quform::get($element, 'defaultValue', ''), "{$name}_default_value", $package, "Field $identifier default value", $type == 'textarea' ? 'AREA' : 'LINE');
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password'), true)) {
                $validators = Quform::get($element, 'validators', array());

                if (is_array($validators)) {
                    foreach ($validators as $validator) {
                        if (is_array($validator)) {
                            $this->registerValidatorStrings($validator, $elementId, $formId, $package);
                        }
                    }
                }
            }

            if (in_array($type, array('select', 'multiselect'), true)) {
                if ($type == 'select' && Quform::get($element, 'noneOption', true)) {
                    do_action('wpml_register_string', Quform::get($element, 'noneOptionText', ''), "{$name}_please_select_option_label", $package, "Field $identifier please select option label", 'LINE');
                }

                if (Quform::get($element, 'enhancedSelectEnabled', false)) {
                    if ($type == 'multiselect' || Quform::get($element, 'enhancedSelectSearch', true)) {
                        do_action('wpml_register_string', Quform::get($element, 'enhancedSelectNoResultsFound', ''), "{$name}_enhanced_select_no_results_found", $package, "Field $identifier enhanced select no results found", 'LINE');
                    }

                    if ($type == 'multiselect') {
                        do_action('wpml_register_string', Quform::get($element, 'enhancedSelectPlaceholder', ''), "{$name}_enhanced_select_placeholder", $package, "Field $identifier enhanced select placeholder", 'LINE');
                    }
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'date', 'time', 'password', 'captcha', 'recaptcha'), true)) {
                if (Quform::get($element, 'required', false) || $type == 'captcha' || $type == 'recaptcha') {
                    do_action('wpml_register_string', Quform::get($element, 'messageRequired', ''), "{$name}_validator_required_message", $package, "Field $identifier validator required message", 'LINE');
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'password'), true)) {
                if (is_numeric(Quform::get($element, 'maxLength', ''))) {
                    do_action('wpml_register_string', Quform::get($element, 'messageLengthTooLong', ''), "{$name}_validator_max_length_message", $package, "Field $identifier validator max length message", 'LINE');
                }
            }

            if ($type == 'file') {
                do_action('wpml_register_string', Quform::get($element, 'browseText', ''), "{$name}_file_browse_text", $package, "Field $identifier file browse text", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageFileUploadRequired', ''), "{$name}_validator_file_required_message", $package, "Field $identifier validator file required message", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageFileNumRequired', ''), "{$name}_validator_file_number_required", $package, "Field $identifier validator file number required message", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageFileTooMany', ''), "{$name}_validator_file_too_many", $package, "Field $identifier validator file too many", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageFileTooBigFilename', ''), "{$name}_validator_file_too_big_filename", $package, "Field $identifier validator file too big filename", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageFileTooBig', ''), "{$name}_validator_file_too_big", $package, "Field $identifier validator file too big", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageNotAllowedTypeFilename', ''), "{$name}_validator_file_not_allowed_type_filename", $package, "Field $identifier validator file not allowed type filename", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageNotAllowedType', ''), "{$name}_validator_file_not_allowed_type", $package, "Field $identifier validator file not allowed type", 'LINE');
            }

            if ($type == 'date') {
                do_action('wpml_register_string', Quform::get($element, 'messageDateInvalidDate', ''), "{$name}_validator_date_invalid_date", $package, "Field $identifier validator date invalid date", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageDateTooEarly', ''), "{$name}_validator_date_too_early", $package, "Field $identifier validator date too early", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageDateTooLate', ''), "{$name}_validator_date_too_late", $package, "Field $identifier validator date too late", 'LINE');
            }

            if ($type == 'time') {
                do_action('wpml_register_string', Quform::get($element, 'messageTimeInvalidTime', ''), "{$name}_validator_time_invalid_time", $package, "Field $identifier validator time invalid time", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageTimeTooEarly', ''), "{$name}_validator_time_too_early", $package, "Field $identifier validator time too early", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageTimeTooLate', ''), "{$name}_validator_time_too_late", $package, "Field $identifier validator time too late", 'LINE');
            }

            if ($type == 'html') {
                do_action('wpml_register_string', Quform::get($element, 'content', ''), "{$name}_html_content", $package, "Field $identifier HTML content", 'LINE');

                if (Quform::get($element, 'showInEmail', false)) {
                    do_action('wpml_register_string', Quform::get($element, 'plainTextContent', ''), "{$name}_plain_text_content", $package, "Field $identifier plain text content", 'LINE');
                }
            }

            if ($type == 'captcha') {
                do_action('wpml_register_string', Quform::get($element, 'messageCaptchaNotMatch', ''), "{$name}_validator_captcha_not_match_message", $package, "Field $identifier validator captcha not match message", 'LINE');
            }

            if ($type == 'recaptcha') {
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaMissingInputSecret', ''), "{$name}_validator_recaptcha_missing_input_secret", $package, "Field $identifier validator recaptcha missing input secret", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaInvalidInputSecret', ''), "{$name}_validator_recaptcha_invalid_input_secret", $package, "Field $identifier validator recaptcha invalid input secret", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaMissingInputResponse', ''), "{$name}_validator_recaptcha_missing_input_response", $package, "Field $identifier validator recaptcha missing input response", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaInvalidInputResponse', ''), "{$name}_validator_recaptcha_invalid_input_response", $package, "Field $identifier validator recaptcha invalid input response", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaError', ''), "{$name}_validator_recaptcha_error", $package, "Field $identifier validator recaptcha error", 'LINE');
                do_action('wpml_register_string', Quform::get($element, 'messageRecaptchaScoreTooLow', ''), "{$name}_validator_recaptcha_score_too_low", $package, "Field $identifier validator recaptcha score too low", 'LINE');
            }
        }
    }

    /**
     * Register the strings for validators
     *
     * @param  array  $validator  The validator configuration
     * @param  int    $elementId  The element ID
     * @param  int    $formId     The form ID
     * @param  array  $package    The package configuration
     */
    protected function registerValidatorStrings(array $validator, $elementId, $formId, array $package)
    {
        $identifier = "{$formId}_{$elementId}";
        $name = "field_{$identifier}_validator";
        $type = Quform::get($validator, 'type', '');

        switch ($type) {
            case 'alpha':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notAlpha', ''), "{$name}_alpha_not_alpha", $package, "Field $identifier validator alpha notAlpha", 'LINE');
                break;
            case 'alphaNumeric':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notAlphaNumeric', ''), "{$name}_alpha_numeric_not_alpha_numeric", $package, "Field $identifier validator alphaNumeric notAlphaNumeric", 'LINE');
                break;
            case 'digits':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notDigits', ''), "{$name}_digits_not_digits", $package, "Field $identifier validator digits notDigits", 'LINE');
                break;
            case 'email':
                do_action('wpml_register_string', Quform::get($validator, 'messages.emailAddressInvalidFormat', ''), "{$name}_email_email_address_invalid_format", $package, "Field $identifier validator email emailAddressInvalidFormat", 'LINE');
                break;
            case 'greaterThan':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notGreaterThan', ''), "{$name}_greater_than_not_greater_than", $package, "Field $identifier validator greaterThan notGreaterThan", 'LINE');
                do_action('wpml_register_string', Quform::get($validator, 'messages.notGreaterThanInclusive', ''), "{$name}_greater_than_not_greater_than_inclusive", $package, "Field $identifier validator greaterThan notGreaterThanInclusive", 'LINE');
                break;
            case 'identical':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notSame', ''), "{$name}_identical_not_same", $package, "Field $identifier validator identical notSame", 'LINE');
                break;
            case 'inArray':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notInArray', ''), "{$name}_in_array_not_in_array", $package, "Field $identifier validator inArray notInArray", 'LINE');
                break;
            case 'length':
                do_action('wpml_register_string', Quform::get($validator, 'messages.lengthTooShort', ''), "{$name}_length_length_too_short", $package, "Field $identifier validator length lengthTooShort", 'LINE');
                do_action('wpml_register_string', Quform::get($validator, 'messages.lengthTooLong', ''), "{$name}_length_length_too_long", $package, "Field $identifier validator length lengthTooLong", 'LINE');
                break;
            case 'lessThan':
                do_action('wpml_register_string', Quform::get($validator, 'messages.notLessThan', ''), "{$name}_less_than_not_less_than", $package, "Field $identifier validator lessThan notLessThan", 'LINE');
                do_action('wpml_register_string', Quform::get($validator, 'messages.notLessThanInclusive', ''), "{$name}_less_than_not_less_than_inclusive", $package, "Field $identifier validator lessThan notLessThanInclusive", 'LINE');
                break;
            case 'duplicate':
                do_action('wpml_register_string', Quform::get($validator, 'messages.isDuplicate', ''), "{$name}_duplicate_is_duplicate", $package, "Field $identifier validator duplicate isDuplicate", 'LINE');
                break;
            case 'regex':
                do_action('wpml_register_string', Quform::get($validator, 'messages.regexNotMatch', ''), "{$name}_regex_regex_not_match", $package, "Field $identifier validator regex regexNotMatch", 'LINE');
                break;
        }
    }

    /**
     * Translate the strings within the given form configuration
     *
     * @param   array  $config
     * @return  array
     */
    public function translateForm(array $config)
    {
        if (is_admin()) {
            return $config;
        }

        $formId = Quform::get($config, 'id', 0);
        $formName = Quform::get($config, 'name', '');
        $package = $this->getPackage($formId, $formName);

        $config['title'] = apply_filters('wpml_translate_string', Quform::get($config, 'title', ''), 'form_title', $package);
        $config['description'] = apply_filters('wpml_translate_string', Quform::get($config, 'description', ''), 'form_description', $package);

        if (Quform::get($config, 'limitEntries', false) && is_numeric($entryLimit = Quform::get($config, 'entryLimit', '')) && $entryLimit > 0) {
            $config['entryLimitReachedMessage'] = apply_filters('wpml_translate_string', Quform::get($config, 'entryLimitReachedMessage', ''), 'entry_limit_reached_message', $package);
            $config['thisFormIsCurrentlyClosed'] = apply_filters('wpml_translate_string', Quform::get($config, 'thisFormIsCurrentlyClosed', ''), 'entry_limit_reached_error_message', $package);
        }

        $config['requiredText'] = apply_filters('wpml_translate_string', Quform::get($config, 'requiredText', ''), 'required_text', $package);

        if (Quform::get($config, 'enableSchedule', false)) {
            if (Quform::isNonEmptyString(Quform::get($config, 'scheduleStart', ''))) {
                $config['scheduleStartMessage'] = apply_filters('wpml_translate_string', Quform::get($config, 'scheduleStartMessage', ''), 'schedule_start_message', $package);
                $config['formIsNotYetOpenForSubmissions'] = apply_filters('wpml_translate_string', Quform::get($config, 'formIsNotYetOpenForSubmissions', ''), 'schedule_start_error_message', $package);
            }

            if (Quform::isNonEmptyString(Quform::get($config, 'scheduleEnd', ''))) {
                $config['scheduleEndMessage'] = apply_filters('wpml_translate_string', Quform::get($config, 'scheduleEndMessage', ''), 'schedule_end_message', $package);
                $config['formIsNoLongerOpenForSubmissions'] = apply_filters('wpml_translate_string', Quform::get($config, 'formIsNoLongerOpenForSubmissions', ''), 'schedule_end_error_message', $package);
            }
        }

        $submitType = Quform::get($config, 'submitType', 'default');

        if ($submitType == 'image') {
            $config['submitImage'] = apply_filters('wpml_translate_string', Quform::get($config, 'submitImage', ''), 'submit_button_image', $package);
        } elseif ($submitType == 'html') {
            $config['submitHtml'] = apply_filters('wpml_translate_string', Quform::get($config, 'submitHtml', ''), 'submit_button_html', $package);
        } else {
            $config['submitText'] = apply_filters('wpml_translate_string', Quform::get($config, 'submitText', ''), 'submit_button_text', $package);
        }

        $elements = Quform::get($config, 'elements', array());

        if (is_array($elements)) {
            if (count($elements) > 1) {
                $nextType = Quform::get($config, 'nextType', 'default');

                if ($nextType == 'image') {
                    $config['nextImage'] = apply_filters('wpml_translate_string', Quform::get($config, 'nextImage', ''), 'next_button_image', $package);
                } elseif ($nextType == 'html') {
                    $config['nextHtml'] = apply_filters('wpml_translate_string', Quform::get($config, 'nextHtml', ''), 'next_button_html', $package);
                } else {
                    $config['nextText'] = apply_filters('wpml_translate_string', Quform::get($config, 'nextText', ''), 'next_button_text', $package);
                }

                $backType = Quform::get($config, 'backType', 'default');

                if ($backType == 'image') {
                    $config['backImage'] = apply_filters('wpml_translate_string', Quform::get($config, 'backImage', ''), 'back_button_image', $package);
                } elseif ($backType == 'html') {
                    $config['backHtml'] = apply_filters('wpml_translate_string', Quform::get($config, 'backHtml', ''), 'back_button_html', $package);
                } else {
                    $config['backText'] = apply_filters('wpml_translate_string', Quform::get($config, 'backText', ''), 'back_button_text', $package);
                }

                if (Quform::get($config, 'pageProgressType', 'numbers') == 'numbers') {
                    $config['pageProgressNumbersText'] = apply_filters('wpml_translate_string', Quform::get($config, 'pageProgressNumbersText', ''), 'page_progress_numbers_text', $package);
                }
            }

            foreach ($elements as $key => $page) {
                if (is_array($page)) {
                    $config['elements'][$key] = $this->translateContainer($page, $formId, $package);
                }
            }
        }

        $notifications = Quform::get($config, 'notifications', array());

        if (is_array($notifications)) {
            foreach ($notifications as $notificationKey => $notification) {
                if (is_array($notification)) {
                    $config['notifications'][$notificationKey] = $this->translateNotification($notification, $formId, $package);
                }
            }
        }

        $confirmations = Quform::get($config, 'confirmations', array());

        if (is_array($confirmations)) {
            foreach ($confirmations as $confirmationKey => $confirmation) {
                if (is_array($confirmation)) {
                    $config['confirmations'][$confirmationKey] = $this->translateConfirmation($confirmation, $formId, $package);
                }
            }
        }

        $config['messageRequired'] = apply_filters('wpml_translate_string', Quform::get($config, 'messageRequired', ''), 'validator_required_message', $package);

        if (Quform::get($config, 'oneEntryPerUser', false)) {
            $config['onlyOneSubmissionAllowed'] = apply_filters('wpml_translate_string', Quform::get($config, 'onlyOneSubmissionAllowed', ''), 'one_entry_per_user_error_message', $package);
        }

        return $config;
    }

    /**
     * Translate the strings within a container
     *
     * @param  array  $container  The container configuration
     * @param  int    $formId     The form ID
     * @param  array  $package    The package configuration
     */
    protected function translateContainer(array $container, $formId, array $package)
    {
        $container = $this->translateElement($container, $formId, $package);

        $elements = Quform::get($container, 'elements', array());

        if (is_array($elements)) {
            foreach($elements as $key => $element) {
                if (is_array($element)) {
                    $type = Quform::get($element, 'type', '');

                    if (in_array($type, array('group', 'row', 'column'), true)) {
                        $container['elements'][$key] = $this->translateContainer($element, $formId, $package);
                    } else {
                        $container['elements'][$key] = $this->translateElement($element, $formId, $package);
                    }
                }
            }
        }

        return $container;
    }

    /**
     * Translate the strings within an element
     *
     * @param  array  $element  The container configuration
     * @param  int    $formId     The form ID
     * @param  array  $package    The package configuration
     */
    protected function translateElement(array $element, $formId, array $package)
    {
        $elementId = Quform::get($element, 'id', 0);
        $name = "field_{$formId}_{$elementId}";
        $type = Quform::get($element, 'type', '');

        if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'html', 'captcha', 'recaptcha', 'submit', 'group', 'page'), true)) {
            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha', 'page'), true)) {
                $element['label'] = apply_filters('wpml_translate_string', Quform::get($element, 'label', ''), "{$name}_label", $package);
            }

            if (in_array($type, array('select', 'checkbox', 'radio', 'multiselect'), true)) {
                $options = Quform::get($element, 'options', array());

                if (is_array($options)) {
                    foreach ($options as $optionKey => $option) {
                        if (is_array($option)) {
                            $optionId = Quform::get($option, 'id', 0);
                            $optionLabel = Quform::get($option, 'label', '');
                            $optgroupOptions = Quform::get($option, 'options');

                            if (in_array($type, array('select', 'multiselect'), true) && is_array($optgroupOptions)) {
                                $element['options'][$optionKey]['label'] = apply_filters('wpml_translate_string', $optionLabel, "{$name}_optgroup_{$optionId}_label", $package);

                                foreach ($optgroupOptions as $optgroupKey => $optgroupOption) {
                                    $optgroupOptionId = Quform::get($optgroupOption, 'id', 0);
                                    $optgroupOptionLabel = Quform::get($optgroupOption, 'label', '');

                                    $element['options'][$optionKey]['options'][$optgroupKey]['label'] = apply_filters('wpml_translate_string', $optgroupOptionLabel, "{$name}_option_{$optgroupOptionId}_label", $package);
                                }
                            } else {
                                $element['options'][$optionKey]['label'] = apply_filters('wpml_translate_string', $optionLabel, "{$name}_option_{$optionId}_label", $package);
                            }
                        }
                    }
                }
            }

            if (in_array($type, array('group', 'page'), true)) {
                $element['title'] = apply_filters('wpml_translate_string', Quform::get($element, 'title', ''), "{$name}_title", $package);
                $element['description'] = apply_filters('wpml_translate_string', Quform::get($element, 'description', ''), "{$name}_description", $package);
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha'), true)) {
                $element['description'] = apply_filters('wpml_translate_string', Quform::get($element, 'description', ''), "{$name}_description", $package);
                $element['descriptionAbove'] = apply_filters('wpml_translate_string', Quform::get($element, 'descriptionAbove', ''), "{$name}_description_above", $package);
            }

            if ($type == 'name') {
                $anyNamePartRequired = false;

                if (Quform::get($element, 'prefixEnabled', false)) {
                    if (Quform::get($element, 'prefixRequired', false)) {
                        $anyNamePartRequired = true;
                    }

                    $prefixOptions = Quform::get($element, 'prefixOptions', array());

                    if (is_array($prefixOptions)) {
                        foreach ($prefixOptions as $prefixOptionKey => $prefixOption) {
                            if (is_array($prefixOption)) {
                                $prefixOptionId = Quform::get($prefixOption, 'id', 0);
                                $prefixOptionLabel = Quform::get($prefixOption, 'label', '');

                                $element['prefixOptions'][$prefixOptionKey]['label'] = apply_filters('wpml_translate_string', $prefixOptionLabel, "{$name}_prefix_option_{$prefixOptionId}_label", $package);
                            }
                        }
                    }

                    $element['prefixSubLabel'] = apply_filters('wpml_translate_string', Quform::get($element, 'prefixSubLabel', ''), "{$name}_prefix_sub_label", $package);
                    $element['prefixSubLabelAbove'] = apply_filters('wpml_translate_string', Quform::get($element, 'prefixSubLabelAbove', ''), "{$name}_prefix_sub_label_above", $package);

                    if (Quform::get($element, 'prefixNoneOption', true)) {
                        $element['prefixNoneOptionText'] = apply_filters('wpml_translate_string', Quform::get($element, 'prefixNoneOptionText', ''), "{$name}_prefix_none_option_text", $package);
                    }
                }

                foreach (array('first', 'middle', 'last', 'suffix') as $part) {
                    if (Quform::get($element, "{$part}Enabled", false)) {
                        if (Quform::get($element, "{$part}Required", false)) {
                            $anyNamePartRequired = true;
                        }

                        $element["{$part}Placeholder"] = apply_filters('wpml_translate_string', Quform::get($element, "{$part}Placeholder", ''), "{$name}_{$part}_placeholder", $package);
                        $element["{$part}SubLabel"] = apply_filters('wpml_translate_string', Quform::get($element, "{$part}SubLabel", ''), "{$name}_{$part}_sub_label", $package);
                        $element["{$part}SubLabelAbove"] = apply_filters('wpml_translate_string', Quform::get($element, "{$part}SubLabelAbove", ''), "{$name}_{$part}_sub_label_above", $package);
                    }
                }

                if ($anyNamePartRequired) {
                    $element['messageRequired'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRequired', ''), "{$name}_validator_required_message", $package);
                }
            }

            if ($type == 'submit') {
                foreach (array('submit', 'next', 'back') as $which) {
                    $submitType = Quform::get($element, "{$which}Type", 'default');

                    if ($submitType == 'image') {
                        $element["{$which}Image"] = apply_filters('wpml_translate_string', Quform::get($element, "{$which}Image", ''), "{$name}_{$which}_button_image", $package);
                    } elseif ($submitType == 'html') {
                        $element["{$which}Html"] = apply_filters('wpml_translate_string', Quform::get($element, "{$which}Html", ''), "{$name}_{$which}_button_html", $package);
                    } else {
                        $element["{$which}Text"] = apply_filters('wpml_translate_string', Quform::get($element, "{$which}Text", ''), "{$name}_{$which}_button_text", $package);
                    }
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'date', 'time', 'password', 'captcha'), true)) {
                $element['placeholder'] = apply_filters('wpml_translate_string', Quform::get($element, 'placeholder', ''), "{$name}_placeholder", $package);
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password', 'captcha', 'recaptcha'), true)) {
                $element['subLabel'] = apply_filters('wpml_translate_string', Quform::get($element, 'subLabel', ''), "{$name}_sub_label", $package);
                $element['subLabelAbove'] = apply_filters('wpml_translate_string', Quform::get($element, 'subLabelAbove', ''), "{$name}_sub_label_above", $package);

                if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password'), true)) {
                    $element['adminLabel'] = apply_filters('wpml_translate_string', Quform::get($element, 'adminLabel', ''), "{$name}_admin_label", $package);
                }

                $element['tooltip'] = apply_filters('wpml_translate_string', Quform::get($element, 'tooltip', ''), "{$name}_tooltip", $package);
            }

            if (in_array($type, array('text', 'textarea', 'email'), true)) {
                $element['defaultValue'] = apply_filters('wpml_translate_string', Quform::get($element, 'defaultValue', ''), "{$name}_default_value", $package);
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'file', 'date', 'time', 'name', 'password'), true)) {
                $validators = Quform::get($element, 'validators', array());

                if (is_array($validators)) {
                    foreach ($validators as $validatorKey => $validator) {
                        if (is_array($validator)) {
                            $element['validators'][$validatorKey] = $this->translateValidator($validator, $elementId, $formId, $package);
                        }
                    }
                }
            }

            if (in_array($type, array('select', 'multiselect'), true)) {
                if ($type == 'select' && Quform::get($element, 'noneOption', true)) {
                    $element['noneOptionText'] = apply_filters('wpml_translate_string', Quform::get($element, 'noneOptionText', ''), "{$name}_please_select_option_label", $package);
                }

                if (Quform::get($element, 'enhancedSelectEnabled', false)) {
                    if ($type == 'multiselect' || Quform::get($element, 'enhancedSelectSearch', true)) {
                        $element['enhancedSelectNoResultsFound'] = apply_filters('wpml_translate_string', Quform::get($element, 'enhancedSelectNoResultsFound', ''), "{$name}_enhanced_select_no_results_found", $package);
                    }

                    if ($type == 'multiselect') {
                        $element['enhancedSelectPlaceholder'] = apply_filters('wpml_translate_string', Quform::get($element, 'enhancedSelectPlaceholder', ''), "{$name}_enhanced_select_placeholder", $package);
                    }
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'multiselect', 'date', 'time', 'password', 'captcha', 'recaptcha'), true)) {
                if (Quform::get($element, 'required', false) || $type == 'captcha' || $type == 'recaptcha') {
                    $element['messageRequired'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRequired', ''), "{$name}_validator_required_message", $package);
                }
            }

            if (in_array($type, array('text', 'textarea', 'email', 'password'), true)) {
                if (is_numeric(Quform::get($element, 'maxLength', ''))) {
                    $element['messageLengthTooLong'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageLengthTooLong', ''), "{$name}_validator_max_length_message", $package);
                }
            }

            if ($type == 'file') {
                $element['browseText'] = apply_filters('wpml_translate_string', Quform::get($element, 'browseText', ''), "{$name}_file_browse_text", $package);
                $element['messageFileUploadRequired'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageFileUploadRequired', ''), "{$name}_validator_file_required_message", $package);
                $element['messageFileNumRequired'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageFileNumRequired', ''), "{$name}_validator_file_number_required", $package);
                $element['messageFileTooMany'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageFileTooMany', ''), "{$name}_validator_file_too_many", $package);
                $element['messageFileTooBigFilename'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageFileTooBigFilename', ''), "{$name}_validator_file_too_big_filename", $package);
                $element['messageFileTooBig'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageFileTooBig', ''), "{$name}_validator_file_too_big", $package);
                $element['messageNotAllowedTypeFilename'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageNotAllowedTypeFilename', ''), "{$name}_validator_file_not_allowed_type_filename", $package);
                $element['messageNotAllowedType'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageNotAllowedType', ''), "{$name}_validator_file_not_allowed_type", $package);
            }

            if ($type == 'date') {
                $element['messageDateInvalidDate'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageDateInvalidDate', ''), "{$name}_validator_date_invalid_date", $package);
                $element['messageDateTooEarly'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageDateTooEarly', ''), "{$name}_validator_date_too_early", $package);
                $element['messageDateTooLate'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageDateTooLate', ''), "{$name}_validator_date_too_late", $package);
            }

            if ($type == 'time') {
                $element['messageTimeInvalidTime'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageTimeInvalidTime', ''), "{$name}_validator_time_invalid_time", $package);
                $element['messageTimeTooEarly'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageTimeTooEarly', ''), "{$name}_validator_time_too_early", $package);
                $element['messageTimeTooLate'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageTimeTooLate', ''), "{$name}_validator_time_too_late", $package);
            }

            if ($type == 'html') {
                $element['content'] = apply_filters('wpml_translate_string', Quform::get($element, 'content', ''), "{$name}_html_content", $package);

                if (Quform::get($element, 'showInEmail', false)) {
                    $element['plainTextContent'] = apply_filters('wpml_translate_string', Quform::get($element, 'plainTextContent', ''), "{$name}_plain_text_content", $package);
                }
            }

            if ($type == 'captcha') {
                $element['messageCaptchaNotMatch'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageCaptchaNotMatch', ''), "{$name}_validator_captcha_not_match_message", $package);
            }

            if ($type == 'recaptcha') {
                $element['messageRecaptchaMissingInputSecret'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaMissingInputSecret', ''), "{$name}_validator_recaptcha_missing_input_secret", $package);
                $element['messageRecaptchaInvalidInputSecret'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaInvalidInputSecret', ''), "{$name}_validator_recaptcha_invalid_input_secret", $package);
                $element['messageRecaptchaMissingInputResponse'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaMissingInputResponse', ''), "{$name}_validator_recaptcha_missing_input_response", $package);
                $element['messageRecaptchaInvalidInputResponse'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaInvalidInputResponse', ''), "{$name}_validator_recaptcha_invalid_input_response", $package);
                $element['messageRecaptchaError'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaError', ''), "{$name}_validator_recaptcha_error", $package);
                $element['messageRecaptchaScoreTooLow'] = apply_filters('wpml_translate_string', Quform::get($element, 'messageRecaptchaScoreTooLow', ''), "{$name}_validator_recaptcha_score_too_low", $package);
            }
        }

        return $element;
    }

    /**
     * Translate a validator
     *
     * @param   array  $validator  The validator configuration
     * @param   int    $elementId  The element ID
     * @param   int    $formId     The form ID
     * @return  array
     */
    public function translateValidator(array $validator, $elementId, $formId, array $package)
    {
        $name = "field_{$formId}_{$elementId}_validator";
        $type = Quform::get($validator, 'type', '');

        switch ($type) {
            case 'alpha':
                $validator['messages']['notAlpha'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notAlpha', ''), "{$name}_alpha_not_alpha", $package);
                break;
            case 'alphaNumeric':
                $validator['messages']['notAlphaNumeric'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notAlphaNumeric', ''), "{$name}_alpha_numeric_not_alpha_numeric", $package);
                break;
            case 'digits':
                $validator['messages']['notDigits'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notDigits', ''), "{$name}_digits_not_digits", $package);
                break;
            case 'email':
                $validator['messages']['emailAddressInvalidFormat'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.emailAddressInvalidFormat', ''), "{$name}_email_email_address_invalid_format", $package);
                break;
            case 'greaterThan':
                $validator['messages']['notGreaterThan'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notGreaterThan', ''), "{$name}_greater_than_not_greater_than", $package);
                $validator['messages']['notGreaterThanInclusive'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notGreaterThanInclusive', ''), "{$name}_greater_than_not_greater_than_inclusive", $package);
                break;
            case 'identical':
                $validator['messages']['notSame'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notSame', ''), "{$name}_identical_not_same", $package);
                break;
            case 'inArray':
                $validator['messages']['notInArray'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notInArray', ''), "{$name}_in_array_not_in_array", $package);
                break;
            case 'length':
                $validator['messages']['lengthTooShort'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.lengthTooShort', ''), "{$name}_length_length_too_short", $package);
                $validator['messages']['lengthTooLong'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.lengthTooLong', ''), "{$name}_length_length_too_long", $package);
                break;
            case 'lessThan':
                $validator['messages']['notLessThan'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notLessThan', ''), "{$name}_less_than_not_less_than", $package);
                $validator['messages']['notLessThanInclusive'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.notLessThanInclusive', ''), "{$name}_less_than_not_less_than_inclusive", $package);
                break;
            case 'duplicate':
                $validator['messages']['isDuplicate'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.isDuplicate', ''), "{$name}_duplicate_is_duplicate", $package);
                break;
            case 'regex':
                $validator['messages']['regexNotMatch'] = apply_filters('wpml_translate_string', Quform::get($validator, 'messages.regexNotMatch', ''), "{$name}_regex_regex_not_match", $package);
                break;
        }

        return $validator;
    }

    /**
     * Translate a notification
     *
     * @param   array   $notification  The notification configuration
     * @param   int     $formId        The form ID
     * @param   array   $package       The package configuration
     * @return  array
     */
    public function translateNotification(array $notification, $formId, array $package)
    {
        $notificationId = Quform::get($notification, 'id', 0);
        $name = "notification_{$formId}_{$notificationId}";

        $notification['subject'] = apply_filters('wpml_translate_string', Quform::get($notification, 'subject', ''), "{$name}_subject", $package);

        if (in_array($notification['format'], array('html', 'multipart'), true)) {
            $notification['html'] = apply_filters('wpml_translate_string', Quform::get($notification, 'html', ''), "{$name}_html", $package);
        }

        if (in_array($notification['format'], array('text', 'multipart'), true)) {
            $notification['text'] = apply_filters('wpml_translate_string', Quform::get($notification, 'text', ''), "{$name}_text", $package);
        }

        return $notification;
    }

    /**
     * Translate a confirmation
     *
     * @param   array   $confirmation  The confirmation configuration
     * @param   int     $formId        The form ID
     * @param   array   $package       The package configuration
     * @return  array
     */
    public function translateConfirmation(array $confirmation, $formId, array $package)
    {
        $confirmationId = Quform::get($confirmation, 'id', 0);
        $name = "confirmation_{$formId}_{$confirmationId}";
        $type = Quform::get($confirmation, 'type', '');

        if (in_array($type, array('message', 'message-redirect-page', 'message-redirect-url'), true)) {
            $confirmation['message'] = apply_filters('wpml_translate_string', Quform::get($confirmation, 'message', ''), "{$name}_message", $package);
        }

        return $confirmation;
    }

    /**
     * Delete translations when a form is deleted
     *
     * @param int $formId
     */
    public function deletePackage($formId)
    {
        do_action('wpml_delete_package', $formId, 'Quform Form');
    }
}

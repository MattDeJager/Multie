<?php

namespace boost\multie\constants;

use craft\base\Field;
use craft\models\EntryType;

class TranslationMethods
{

    // Method to get all section types
    public static function getTranslationMethods(): array
    {
        return [
            Field::TRANSLATION_METHOD_NONE => 'Not translatable',
            Field::TRANSLATION_METHOD_SITE => 'Translate for each site',
            Field::TRANSLATION_METHOD_SITE_GROUP => 'Translate for each site group',
            Field::TRANSLATION_METHOD_LANGUAGE => 'Translate for each language',
//            Field::TRANSLATION_METHOD_CUSTOM => 'Custom...',
        ];
    }
}
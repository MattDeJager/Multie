<?php

namespace boost\multie\helpers;

use boost\multie\controllers\FieldsController;
use craft\models\EntryType;

class EntryTypesVueAdminTableHelper extends VueAdminTableHelper
{
    public static function actions(): array
    {
        $titleTranslationMethodActions  = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Title Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions(FieldsController::ACTION_UPDATE, 'titleTranslationMethod'),
            'translate'
        );

        $slugTranslationMethodActions = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Slug Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions(FieldsController::ACTION_UPDATE, 'slugTranslationMethod'),
            'translate'
        );

        // TODO:  Add actions for show_title_field and show_slug_field

        return [
            $titleTranslationMethodActions,
            $slugTranslationMethodActions
        ];
    }

    public static function data($entries): array
    {

        $tableData = [];

        // TODO:  Make status dynamic
        $status = 'enabled';

        /** @var EntryType $entryType */
        foreach ($entries as $entryType) {
            $tableData[] = [
                'id' => $entryType->id,
                'title' => "<a class='cell-bold' href='/admin/settings/entry-types/" . $entryType->id . "'>" . $entryType->name . "</a>",
                'title_translation_method' => $entryType->titleTranslationMethod,
                'slug_translation_method' => $entryType->slugTranslationMethod,
                'show_title_field' => "<span class='status " . $status . "'></span>",
                'show_slug_field' => "<span class='status " . $status . "'></span>",
            ];
        }

        return $tableData;
    }

    public static function columns(): array
    {
        return [
            VueAdminTableHelper::createColumn('title', 'Name'),
            VueAdminTableHelper::createColumn('show_title_field', 'Show Title Field'),
            VueAdminTableHelper::createColumn('show_slug_field', 'Show Slug Field'),
            VueAdminTableHelper::createColumn('title_translation_method', 'Title Translation Method'),
            VueAdminTableHelper::createColumn('slug_translation_method', 'Slug Translation Method'),
        ];

    }


}
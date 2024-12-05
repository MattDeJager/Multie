<?php

namespace boost\multie\helpers;

use boost\multie\controllers\EntryTypesController;
use craft\models\EntryType;

class EntryTypesVueAdminTableHelper extends VueAdminTableHelper
{
    public static function actions(): array
    {
        $titleTranslationMethodActions  = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Title Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions(EntryTypesController::ACTION_UPDATE, 'titleTranslationMethod'),
            'translate'
        );

        $slugTranslationMethodActions = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Slug Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions(EntryTypesController::ACTION_UPDATE, 'slugTranslationMethod'),
            'translate'
        );

        $showTitleFieldActions = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Show Title Field'),
            [
                self::getActionArray('Show', EntryTypesController::ACTION_UPDATE, 'fields', [['handle' => 'hasTitleField','value' => 1]]),
                self::getActionArray("Don't Show", EntryTypesController::ACTION_UPDATE, 'fields', [['handle' => 'hasTitleField','value' => 0]]),
            ],
            'translate'
        );

        $showSlugFieldAction = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Show Slug Field'),
            [
                self::getActionArray('Show', EntryTypesController::ACTION_UPDATE, 'fields', [['handle' => 'showSlugField','value' => 1]]),
                self::getActionArray("Don't Show", EntryTypesController::ACTION_UPDATE, 'fields', [['handle' => 'showSlugField','value' => 0]]),
            ],
            'translate'
        );


        return [
            $titleTranslationMethodActions,
            $slugTranslationMethodActions,
            $showTitleFieldActions,
            $showSlugFieldAction
        ];
    }

    public static function data($entries): array
    {

        $tableData = [];

        // TODO: Add lightswitches here
        /** @var EntryType $entryType */
        foreach ($entries as $entryType) {
            $showTitleField = $entryType->hasTitleField ? 'enabled' : 'disabled';
            $showSlugField = $entryType->showSlugField ? 'enabled' : 'disabled';
            $tableData[] = [
                'id' => $entryType->id,
                'title' => "<a class='cell-bold' href='/admin/settings/entry-types/" . $entryType->id . "'>" . $entryType->name . "</a>",
                'title_translation_method' => $entryType->titleTranslationMethod,
                'slug_translation_method' => $entryType->slugTranslationMethod,
                'show_title_field' => "<span class='status " . $showTitleField . "'></span>",
                'show_slug_field' => "<span class='status " . $showSlugField . "'></span>",
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
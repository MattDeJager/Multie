<?php

namespace boost\multie\helpers;

use boost\multie\constants\SectionTypes;
use boost\multie\controllers\SectionsController;
use Craft;
use craft\helpers\UrlHelper;
use craft\models\Section;

class SectionGeneralSettingsVueAdminTableHelper extends VueAdminTableHelper
{

    private static array $propagationMethods = [
        'all' => 'Save entries to all sites enabled for this section',
        'siteGroup' => 'Save entries to other sites in the same site group',
        'language' => 'Save entries to other sites with the same language',
        'none' => 'Only save entries to the site they were created in',
        'custom' => 'Let each entry choose which sites it should be saved to',
    ];


    public static function actions($type = null): array
    {

        $propagationMethodActions = [];
        foreach (self::$propagationMethods as $key => $value) {
            $propagationMethodActions[] = VueAdminTableHelper::getActionArray($value, SectionsController::ACTION_UPDATE_PROPAGATION_METHOD, 'propagationMethod', $key);
        }

        $actions = [
            // SECTION ENTRY TYPE CONFIG
            VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Entry Types: Title Translation Method'),
                VueAdminTableHelper::getTranslationMethodActions(SectionsController::ACTION_UPDATE_ENTRY_TYPES, 'titleTranslationMethod')
            ),

            VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Entry Types: Slug Translation Method'),
                VueAdminTableHelper::getTranslationMethodActions(SectionsController::ACTION_UPDATE_ENTRY_TYPES, 'slugTranslationMethod')
            ),
        ];

        if ($type !== SectionTypes::SINGLE) {
            $actions[] = VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Propagation Method'),
                $propagationMethodActions
            );
        }

        return $actions;
    }

    public static function data($entries): array
    {

        $tableData = [];
        /** @var Section $section */
        foreach ($entries as $section) {

            $propagationMethod = self::$propagationMethods[$section->propagationMethod] ?? "";

            if ($section->type == SectionTypes::SINGLE) {
                $propagationMethod = "-";
            }

            $tableData[] = [
                'id' => $section->id,
                'title' => "<a class='cell-bold' href='/admin/settings/sections/" . $section->id . "'>" . $section->name . "</a>",
                'url' => UrlHelper::url('multie/sections/edit/' . $section->id),
                'name' => htmlspecialchars(Craft::t('site', $section->name)),
                'propagation_method' => $propagationMethod
            ];
        }

        return $tableData;
    }

    public static function columns(): array
    {
        return [
            VueAdminTableHelper::createColumn('title', 'Name'),
            VueAdminTableHelper::createColumn('propagation_method', 'Propagation Method'),
        ];
    }


}
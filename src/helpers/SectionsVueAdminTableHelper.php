<?php

namespace boost\multie\helpers;

use boost\multie\controllers\SectionsController;
use Craft;
use craft\helpers\UrlHelper;

class SectionsVueAdminTableHelper extends VueAdminTableHelper
{
    const ENABLED_STATUS = 'enabled';
    const DISABLED_STATUS = 'disabled';

    public static function actions(): array
    {
        $sites = Craft::$app->sites->getAllSites();
        $currentSiteHandle = Craft::$app->request->get('site', SectionsController::DEFAULT_SITE_HANDLE);
        $entryUriFormatActions = [];
        $templateActions = [];
        $statusActions = [
            VueAdminTableHelper::getActionArray('Enabled', SectionsController::ACTION_UPDATE_STATUS, 'status', self::ENABLED_STATUS, self::ENABLED_STATUS),
            VueAdminTableHelper::getActionArray('Disabled', SectionsController::ACTION_UPDATE_STATUS, 'status', self::DISABLED_STATUS, self::DISABLED_STATUS),
        ];

        foreach ($sites as $site) {
            if ($site->handle !== $currentSiteHandle) {
                $entryUriFormatActions[] = VueAdminTableHelper::getActionArray(
                    "Use from $site->name",
                    SectionsController::ACTION_COPY_SETTINGS,
                    'site',
                    [
                        'handle' => $site->handle,
                        'settings' => ['uriFormat', 'hasUrls']
                    ]
                );
                $templateActions[] = VueAdminTableHelper::getActionArray(
                    "Use from $site->name",
                    SectionsController::ACTION_COPY_SETTINGS,
                    'site',
                    [
                        'handle' => $site->handle,
                        'settings' => ['template', 'hasUrls']
                    ]
                );
            }
        }

        return [
            VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Set Status'), $statusActions),
            VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Entry URI Format'), $entryUriFormatActions, "settings"),
            VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Template'), $templateActions, "settings"),
            // SECTION ENTRY TYPE CONFIG
            VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Entry Type: Title Translation Method'),
                VueAdminTableHelper::getTranslationMethodActions(SectionsController::ACTION_UPDATE_ENTRY_TYPES, 'titleTranslationMethod')
            ),

            VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Entry Type: Slug Translation Method'),
                VueAdminTableHelper::getTranslationMethodActions(SectionsController::ACTION_UPDATE_ENTRY_TYPES, 'slugTranslationMethod')
            ),


        ];
    }

    public static function data($entries): array
    {

        $siteHandle = Craft::$app->request->get('site');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);

        $tableData = [];

        foreach ($entries as $section) {
            $sectionSiteSettings = $section->getSiteSettings()[$site->id] ?? null;
            $status = isset($sectionSiteSettings) ? self::ENABLED_STATUS : self::DISABLED_STATUS;

            $tableData[] = [
                'id' => $section->id,
                'title' => "<span class='status " . $status . "'></span><a class='cell-bold' href='/admin/settings/sections/" . $section->id . "'>" . $section->name . "</a>",
                'url' => UrlHelper::url('multie/sections/edit/' . $section->id),
                'name' => htmlspecialchars(Craft::t('site', $section->name)),
                'status' => $status,
                'entry_uri_format' => $sectionSiteSettings->uriFormat ?? "",
                'template' => $sectionSiteSettings->template ?? "",
            ];
        }

        return $tableData;
    }

    public static function columns(): array
    {
        // TODO: There should be a helped method for the below
        return [
            ['name' => 'title', 'title' => Craft::t('app', 'Name')],
            ['name' => 'entry_uri_format', 'title' => Craft::t('multie', 'Entry URI Format')],
            ['name' => 'template', 'title' => Craft::t('multie', 'Template')],
        ];
    }


}
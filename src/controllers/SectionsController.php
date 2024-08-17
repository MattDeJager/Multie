<?php

namespace boost\multie\controllers;

use boost\multie\services\SectionsService;
use Craft;
use craft\helpers\UrlHelper;
use craft\models\Site;
use craft\web\Controller;
use boost\multie\helpers\VueAdminTableHelper;
use boost\multie\Plugin;

class SectionsController extends Controller
{
    const DEFAULT_SITE_HANDLE = 'default';
    const ENABLED_STATUS = 'enabled';
    const DISABLED_STATUS = 'disabled';

    const PATH = Plugin::HANDLE . '/sections';

    // ACTIONS
    const ACTION_UPDATE_STATUS = self::PATH . '/update-status';
    const ACTION_UPDATE_ENTRY_TYPES = self::PATH . '/update-entry-types';
    const ACTION_COPY_SETTINGS = self::PATH . '/copy-settings';


    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();

        $site = $this->getSiteFromRequest();
        $sections = Craft::$app->sections->getAllSections();

        $columns = $this->getColumns();
        $tableData = $this->getTableData($sections, $site);
        $actions = $this->getTableActions();

        return $this->renderTemplate(self::PATH . '/index.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns,
        ]);
    }

    public function actionUpdateStatus(): \yii\web\Response
    {
        $sectionsService = Plugin::getInstance()->section;

        $site = $this->getSiteFromRequest();
        $sectionIds = Craft::$app->request->post("ids");
        $status = json_decode(Craft::$app->request->getBodyParam('status'));

        $sectionsService->updateSectionsStatusForSite($sectionIds, $status, $site);

        return $this->redirect('multie/sections');
    }

    public function actionUpdateEntryTypes(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;
        $sectionIds = Craft::$app->request->post("ids");
        $fields = json_decode(Craft::$app->request->getBodyParam('fields'), true);

        $sectionsService->updateAllEntryTypesForSections($sectionIds, $fields);

        return $this->redirect(self::PATH);
    }

    public function actionCopySettings(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $site = $this->getSiteFromRequest();
        $config = json_decode(Craft::$app->request->getBodyParam('site'), true);
        $siteToCopy = Craft::$app->sites->getSiteByHandle($config['handle']);
        $sectionIds = Craft::$app->request->post("ids");

        $sectionsService->copySectionSettingsFromSite($config['settings'], $sectionIds, $siteToCopy, $site);

        return $this->redirect(self::PATH);
    }


    private function getSiteFromRequest(): Site
    {
        $siteHandle = Craft::$app->request->get('site', self::DEFAULT_SITE_HANDLE);
        return Craft::$app->sites->getSiteByHandle($siteHandle);
    }

    private function getColumns(): array
    {
        return [
            ['name' => 'title', 'title' => Craft::t('app', 'Name')],
            ['name' => 'entry_uri_format', 'title' => Craft::t('multie', 'Entry URI Format')],
            ['name' => 'template', 'title' => Craft::t('multie', 'Template')],
        ];
    }

    private function getTableData(array $sections, Site $site): array
    {
        $tableData = [];

        foreach ($sections as $section) {
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

    private function getTableActions(): array
    {
        $sites = Craft::$app->sites->getAllSites();
        $currentSiteHandle = Craft::$app->request->get('site', self::DEFAULT_SITE_HANDLE);
        $entryUriFormatActions = [];
        $templateActions = [];
        $statusActions = [
            VueAdminTableHelper::getActionArray('Enabled', self::ACTION_UPDATE_STATUS, 'status', self::ENABLED_STATUS, self::ENABLED_STATUS),
            VueAdminTableHelper::getActionArray('Disabled', self::ACTION_UPDATE_STATUS, 'status', self::DISABLED_STATUS, self::DISABLED_STATUS),
        ];

        foreach ($sites as $site) {
            if ($site->handle !== $currentSiteHandle) {
                $entryUriFormatActions[] = VueAdminTableHelper::getActionArray(
                    "Use from $site->name",
                    self::ACTION_COPY_SETTINGS,
                    'site',
                    [
                        'handle' => $site->handle,
                        'settings' => ['uriFormat', 'hasUrls']
                    ]
                );
                $templateActions[] = VueAdminTableHelper::getActionArray(
                    "Use from $site->name",
                    self::ACTION_COPY_SETTINGS,
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
                VueAdminTableHelper::getTranslationMethodActions(self::ACTION_UPDATE_ENTRY_TYPES, 'titleTranslationMethod')
            ),

            VueAdminTableHelper::getActionsArray(
                \Craft::t('app', 'Entry Type: Slug Translation Method'),
                VueAdminTableHelper::getTranslationMethodActions(self::ACTION_UPDATE_ENTRY_TYPES, 'slugTranslationMethod')
            ),


        ];
    }
}

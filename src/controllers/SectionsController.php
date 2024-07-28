<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\models\Site;
use craft\web\Controller;
use matthewdejager\craftmultie\helpers\VueAdminTableHelper;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\SectionsService;

class SectionsController extends Controller
{
    public function actionIndex(): \yii\web\Response
    {
        // TODO: Update the presenation of Template & Entry URI format to code

        $this->requireAdmin();

        $siteHandle = Craft::$app->request->get('site', 'default'); // Default to 'default' if not provided
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);

        $tableData = [];
        $sections = Craft::$app->sections->getAllSections();

        $columns = [
            ['name' => 'title', 'title' => Craft::t('app', 'Name')],
            ['name' => 'entry_uri_format', 'title' => Craft::t('multie', 'Entry URI Format')],
            ['name' => 'template', 'title' => Craft::t('multie', 'Template')],
        ];


        foreach ($sections as $section) {
            $sectionSiteSettings = $section->getSiteSettings()[$site->id] ?? null;
            $status = isset($sectionSiteSettings) ? 'enabled' : 'disabled';

            $tableData[] = [
                'id' => $section->id,
                'title' => "<span class='status ". $status ."'></span><a class='cell-bold' href='/admin/settings/sections/" . $section->id . "'>" . $section->name . "</a>",
                'url' => UrlHelper::url('multie/sections/edit/' . $section->id),
                'name' => htmlspecialchars(Craft::t('site', $section->name)),
                'status' => $status,
                'entry_uri_format' => $sectionSiteSettings->uriFormat ?? "",
                'template' => $sectionSiteSettings->template ?? ""
            ];
        }

        $actions = $this->getTableActions();

        return $this->renderTemplate('multie/sections/index.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns,
        ]);
    }

    public function actionUpdateStatus(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $siteHandle = Craft::$app->request->get('site', 'default');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        $sectionIds = Craft::$app->request->post("ids");
        $status = json_decode(Craft::$app->request->getBodyParam('status'));

        $sectionsService->updateSectionsStatusForSite($sectionIds, $status, $site);

        return $this->redirect('multie/sections');
    }

    public function actionCopySettings(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $siteHandle = Craft::$app->request->get('site', 'default');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $sectionIds = Craft::$app->request->post("ids");
        $siteToCopyHandle = json_decode(Craft::$app->request->getBodyParam('site'));
        $siteToCopy = Craft::$app->sites->getSiteByHandle($siteToCopyHandle);
        $sectionsService->copySettingsFromSite($sectionIds, $siteToCopy, $site);

        return $this->redirect('multie/sections');
    }

    private function getTableActions(): array
    {
        $sites = Craft::$app->sites->getAllSites();
        $currentSiteHandle = Craft::$app->request->get('site', 'default');
        $settings = [];

        foreach ($sites as $site) {
            if ($site->handle !== $currentSiteHandle) {
                $settings[] = VueAdminTableHelper::getActionArray(
                    "Copy settings from $site->name",
                    'multie/sections/copy-settings',
                    'site',
                    $site->handle
                );
            }
        }

        return [
            [
                'label' => \Craft::t('app', 'Set Status'),
                'actions' => [
                    VueAdminTableHelper::getActionArray('Enabled', 'multie/sections/update-status', 'status', 'enabled', 'enabled'),
                    VueAdminTableHelper::getActionArray('Disabled', 'multie/sections/update-status', 'status', 'disabled', 'disabled'),
                ],
            ],
            [
                'icon' => 'settings',
                'actions' => $settings
            ],
        ];
    }

}

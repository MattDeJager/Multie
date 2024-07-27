<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\helpers\VueAdminTableHelper;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\SectionsService;

class SectionsController extends Controller
{
    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();

        // Get the 'site' parameter from the URL
        $siteHandle = Craft::$app->request->get('site', 'default'); // Default to 'default' if not provided

        // Get the site object by its handle
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);

        $tableData = [];
        $sections = Craft::$app->sections->getAllSections();

        foreach ($sections as $section) {
            $sectionSiteSettings = $section->getSiteSettings()[$site->id] ?? null;
            $status = isset($sectionSiteSettings) ? 1 : 0;

            $tableData[] = [
                'id' => $section->id,
                'title' => Craft::t('site', $section->name),
                'url' => UrlHelper::url('multie/sections/edit/' . $section->id),
                'name' => htmlspecialchars(Craft::t('site', $section->name)),
                'status' => $status,
                'entry_uri_format' => $sectionSiteSettings->uriFormat ?? "", // Set the entry_uri_format field to "test"
                'template' => $sectionSiteSettings->template ?? "", // Set the template field to "test"
            ];
        }

        $actions = [
            [
                'label' => \Craft::t('app', 'Set Status'),
                'actions' => [
                    VueAdminTableHelper::getActionArray('Enabled', 'multie/sections/update-status', 'status', ['enabled'], 'enabled'),
                    VueAdminTableHelper::getActionArray('Disabled', 'multie/sections/update-status', 'status', ['disabled'], 'disabled'),
                ],
            ],
            [
                'icon' => 'settings',
                'actions' => [
                    VueAdminTableHelper::getActionArray('Copy settings from default site', 'multie/sections/copy-settings', 'site', ['default'], 'settings'),
                ],
            ],
        ];

        return $this->renderTemplate('multie/sections/index.twig', [
            "tableData" => $tableData,
            'actions' => $actions,
        ]);
    }

    public function actionUpdateStatus(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $siteHandle = Craft::$app->request->get('site', 'default');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        $sectionIds = Craft::$app->request->post("ids");
        $status = Craft::$app->request->getBodyParam('status');

        $sectionsService->updateSectionsStatusForSite($sectionIds, $status, $site);

        return $this->redirect('multie/sections');
    }

    public function actionCopySettings(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $siteHandle = Craft::$app->request->get('site', 'default');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);

        $sectionsService = Plugin::getInstance()->section;

        $sectionIds = Craft::$app->request->post("ids");
        $siteToCopyHandle = Craft::$app->request->getBodyParam('site');
        $siteToCopy = Craft::$app->sites->getSiteByHandle($siteToCopyHandle);

        $sectionsService->copySettingsFromSite($sectionIds, $siteToCopy, $site);

        return $this->redirect('multie/sections');
    }
}

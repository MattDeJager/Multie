<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\SectionsService;

class SectionsController extends Controller
{

    public function actionUpdateStatus(): \yii\web\Response
    {
        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $siteHandle = Craft::$app->request->get('site', 'default');
        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        $sectionIds = Craft::$app->request->post("ids");
        $status = Craft::$app->request->getBodyParam('status');

        $sectionsService->updateSectionsStatusForSite($sectionIds, $status, $site);

        return $this->redirect('multie');

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

        return $this->redirect('multie');

    }
}
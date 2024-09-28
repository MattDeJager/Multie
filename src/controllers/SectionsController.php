<?php

namespace boost\multie\controllers;

use boost\multie\constants\SectionTypes;
use boost\multie\helpers\SectionGeneralSettingsVueAdminTableHelper;
use boost\multie\helpers\SectionSiteSettingsVueAdminTableHelper;
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
    const PATH = Plugin::HANDLE . '/sections';

    // ACTIONS
    const ACTION_UPDATE_STATUS = self::PATH . '/update-status';
    const ACTION_UPDATE_ENTRY_TYPES = self::PATH . '/update-entry-types';
    const ACTION_COPY_SETTINGS = self::PATH . '/copy-settings';
    const ACTION_UPDATE_PROPAGATION_METHOD = self::PATH . '/update-propagation-method';


    public function actionSiteSettingsIndex(string $type = null): \yii\web\Response
    {

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $sections = $sectionsService->getSectionsByType($type);
        $columns = SectionSiteSettingsVueAdminTableHelper::columns();
        $tableData = SectionSiteSettingsVueAdminTableHelper::data($sections);
        $actions = Craft::$app->user->checkPermission(Plugin::PERMISSION_EDIT_SECTIONS) ?
            SectionSiteSettingsVueAdminTableHelper::actions() :
            false;

        return $this->renderTemplate(self::PATH . '/site-settings.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns,
            'sectionTypes' => SectionTypes::getSectionTypes(),
        ]);
    }

    public function actionGeneralSettingsIndex(string $type = null): \yii\web\Response
    {

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $sections = $sectionsService->getSectionsByType($type);
        $columns = SectionGeneralSettingsVueAdminTableHelper::columns();
        $tableData = SectionGeneralSettingsVueAdminTableHelper::data($sections);

        $actions = Craft::$app->user->checkPermission(Plugin::PERMISSION_EDIT_SECTIONS) ?
            SectionGeneralSettingsVueAdminTableHelper::actions($type) :
            [];

        return $this->renderTemplate(self::PATH . '/general-settings.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns,
            'selected',
            'sectionTypes' => SectionTypes::getSectionTypes(),
        ]);
    }

    public function actionUpdateStatus(): \yii\web\Response
    {
        $this->requirePermission(Plugin::PERMISSION_EDIT_SECTIONS);

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $site = $this->getSiteFromRequest();
        $sectionIds = Craft::$app->request->post("ids");
        $status = json_decode(Craft::$app->request->getBodyParam('status'));

        $sectionsService->updateSectionsStatusForSite($sectionIds, $status, $site);

        return $this->redirect('multie/sections');
    }

    public function actionUpdateEntryTypes(): \yii\web\Response
    {
        $this->requirePermission(Plugin::PERMISSION_EDIT_SECTIONS);

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;
        $sectionIds = Craft::$app->request->post("ids");
        $fields = json_decode(Craft::$app->request->getBodyParam('fields'), true);

        $sectionsService->updateAllEntryTypesForSections($sectionIds, $fields);

        return $this->redirect(self::PATH);
    }

    public function actionCopySettings(): \yii\web\Response
    {
        $this->requirePermission(Plugin::PERMISSION_EDIT_SECTIONS);

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $site = $this->getSiteFromRequest();
        $config = json_decode(Craft::$app->request->getBodyParam('site'), true);
        $siteToCopy = Craft::$app->sites->getSiteByHandle($config['handle']);
        $sectionIds = Craft::$app->request->post("ids");

        $sectionsService->copySectionSettingsFromSite($config['settings'], $sectionIds, $siteToCopy, $site);

        return $this->redirect(self::PATH);
    }

    public function actionUpdatePropagationMethod(): \yii\web\Response
    {
        $this->requirePermission(Plugin::PERMISSION_EDIT_SECTIONS);

        /** @var SectionsService $sectionsService */
        $sectionsService = Plugin::getInstance()->section;

        $site = $this->getSiteFromRequest();
        $sectionIds = Craft::$app->request->post("ids");
        $propagationMethod = json_decode(Craft::$app->request->post("propagationMethod"));
        $sectionsService->updatePropagationMethodForSections($sectionIds, $propagationMethod, $site);

        return $this->redirect(Craft::$app->getRequest()->getReferrer());
    }

    private function getSiteFromRequest(): Site
    {
        $siteHandle = Craft::$app->request->get('site', self::DEFAULT_SITE_HANDLE);
        return Craft::$app->sites->getSiteByHandle($siteHandle);
    }
}

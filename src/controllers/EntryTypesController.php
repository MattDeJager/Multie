<?php

namespace boost\multie\controllers;

use boost\multie\helpers\EntryTypesVueAdminTableHelper;
use boost\multie\helpers\SectionSiteSettingsVueAdminTableHelper;
use craft\web\Controller;
use boost\multie\Plugin;
use nystudio107\seomatic\models\jsonld\DDxElement;

class EntryTypesController extends Controller
{
    const PATH = Plugin::HANDLE . '/entry-types';

    const ACTION_UPDATE = self::PATH . '/update';
    public function actionIndex(string $type = null): \yii\web\Response
    {
        $tableData = EntryTypesVueAdminTableHelper::data(\Craft::$app->entries->getAllEntryTypes());
        $actions = EntryTypesVueAdminTableHelper::actions();
        $columns = EntryTypesVueAdminTableHelper::columns();

        return $this->renderTemplate(self::PATH . '/index.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns
        ]);
    }


    public function actionUpdate(): \yii\web\Response
    {

        dd('update');
//        TODO: Add permissions
//        $this->requirePermission(Plugin::PERMISSION_EDIT_FIELDS);

        $entryTypeIds = Craft::$app->request->post("ids");

        dd($entryTypeIds);
//        $fieldConfig = json_decode(Craft::$app->request->getBodyParam('fields'), true);
//        $fieldService = Plugin::getInstance()->field;
//
//        $fieldService->updateFields($fieldIds, $fieldConfig);

        // Get the referrer URL
        $referrer = Craft::$app->request->referrer;

        // Redirect to the referrer URL if it exists, otherwise redirect to a default URL
        return $this->redirect($referrer ?: 'multie/fields');
    }

}

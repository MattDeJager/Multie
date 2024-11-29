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

}

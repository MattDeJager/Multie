<?php

namespace boost\multie\controllers;

use boost\multie\helpers\EntryTypesVueAdminTableHelper;
use boost\multie\services\EntryTypeService;
use Craft;
use craft\web\Controller;
use boost\multie\Plugin;
use craft\web\Request;

class EntryTypesController extends Controller
{
    const PATH = Plugin::HANDLE . '/entry-types';

    const ACTION_UPDATE = self::PATH . '/update';

    public function actionIndex(string $type = null): \yii\web\Response
    {
        $tableData = EntryTypesVueAdminTableHelper::data(\Craft::$app->entries->getAllEntryTypes());
        $actions = Craft::$app->user->checkPermission(Plugin::PERMISSION_EDIT_ENTRY_TYPES) ?
            EntryTypesVueAdminTableHelper::actions() :
            false;
        $columns = EntryTypesVueAdminTableHelper::columns();

        return $this->renderTemplate(self::PATH . '/index.twig', [
            'tableData' => $tableData,
            'actions' => $actions,
            'columns' => $columns
        ]);
    }


    public function actionUpdate(): \yii\web\Response
    {
        $this->requirePermission(Plugin::PERMISSION_EDIT_ENTRY_TYPES);

        /** @var Request $request */
        $request = Craft::$app->request;

        $entryTypeIds = $request->post("ids");
        $entryTypeConfig = json_decode($request->getBodyParam('fields'), true);

        /** @var EntryTypeService $entryTypeService */
        $entryTypeService = Plugin::getInstance()->entryType;

        $entryTypeService->updateEntryTypes($entryTypeIds, $entryTypeConfig);

        // Get the referrer URL
        $referrer = Craft::$app->request->referrer;

        // Redirect to the referrer URL if it exists, otherwise redirect to a default URL
        return $this->redirect($referrer ?: 'multie/fields');
    }

}

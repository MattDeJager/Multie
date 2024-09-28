<?php

namespace boost\multie\controllers;

use boost\multie\helpers\FieldsVueAdminTableHelper;
use Craft;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\web\Controller;
use boost\multie\helpers\VueAdminTableHelper;
use boost\multie\models\FieldGroup;
use boost\multie\Plugin;
use boost\multie\services\FieldGroupService;
use boost\multie\services\FieldsService;

class FieldsController extends Controller
{
    const PATH = Plugin::HANDLE . '/fields';

    // ACTIONS
    const ACTION_UPDATE = self::PATH . '/update';

    public function actionIndex(int $fieldGroupId = null): \yii\web\Response
    {

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;
        /** @var FieldGroupService $fieldGroupService */
        $fieldGroupService = Plugin::getInstance()->fieldGroup;

        $fieldGroup = $fieldGroupService->getFieldGroupById($fieldGroupId);
        $fieldGroups = $fieldGroupService->getAllFieldGroups();
        $fields = $fieldService->getFieldsInGroup($fieldGroup);

        $field = Craft::$app->fields->getFieldById(1);

        return $this->renderTemplate('multie/fields/index.twig', [
            "field" => $field,
            "fieldGroups" => $fieldGroups,
            "actions" => FieldsVueAdminTableHelper::actions($fieldGroup),
            "tableData" => FieldsVueAdminTableHelper::data($fields),
        ]);
    }

    public function actionUpdate(): \yii\web\Response
    {
        $fieldIds = Craft::$app->request->post("ids");
        $fieldConfig = json_decode(Craft::$app->request->getBodyParam('fields'), true);
        $fieldService = Plugin::getInstance()->field;

        $fieldService->updateFields($fieldIds, $fieldConfig);

        // Get the referrer URL
        $referrer = Craft::$app->request->referrer;

        // Redirect to the referrer URL if it exists, otherwise redirect to a default URL
        return $this->redirect($referrer ?: 'multie/fields');
    }

}

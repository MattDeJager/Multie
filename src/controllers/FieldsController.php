<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\FieldsService;
use matthewdejager\craftmultie\services\SectionsService;

class FieldsController extends Controller
{

    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();

        $fieldGroups = [
            [
                "name" => "All Fields",
            ],
            [
                "name" => "Simple Fields",
            ],
            [
                "name" => "Base Relations Fields",
            ],
            [
                "name" => "Matrix Fields",
            ],
        ];

        $field = Craft::$app->fields->getFieldById(1);


        return $this->renderTemplate('multie/fields/index.twig', [
            "field" => $field,
            "fieldGroups" => $fieldGroups
        ]);
    }

    public function actionUpdateAll(): \yii\web\Response
    {
        $this->requireAdmin();

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;

        $translationMethod = Craft::$app->request->getBodyParam('translationMethod');
        $propagationMethod = Craft::$app->request->getBodyParam('propagationMethod');
        $localizeRelations = Craft::$app->request->getBodyParam('localizeRelations');

        $fields = Craft::$app->fields->getAllFields();

        $config = [
            'translationMethod' => $translationMethod,
            'propagationMethod' => $propagationMethod,
            'localizeRelations' => $localizeRelations
        ];

        $fieldService->translateFields($fields, $config);



        $field = Craft::$app->fields->getFieldById(1);


        return $this->renderTemplate('multie/fields/index.twig', ["field" => $field]);
    }




}
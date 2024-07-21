<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\SectionsService;

class FieldsController extends Controller
{

    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();

        $field = Craft::$app->fields->getFieldById(1);


        return $this->renderTemplate('multie/fields/index.twig', ["field" => $field]);
    }

}
<?php

namespace boost\multie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;

class TranslationsController extends Controller
{

    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();


        return $this->renderTemplate('multie/translations/index.twig');
    }

}
<?php

namespace boost\multie\console\controllers;

use Craft;
use craft\helpers\Console;
use yii\console\ExitCode;

/**
 * Fields Controller
 */
class FieldsController extends SiteController
{
    public function actionTranslateFields()
    {
        $this->stdout("Translating all fields..." . PHP_EOL, Console::FG_GREEN);
        $fields = Craft::$app->fields->getAllFields();
        $this->fieldsService->translateFields($fields);
        return ExitCode::OK;
    }


}

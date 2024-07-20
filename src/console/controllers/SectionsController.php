<?php

namespace matthewdejager\craftmultie\console\controllers;

use craft\errors\EntryTypeNotFoundException;
use craft\helpers\Console;
use craft\models\Section_SiteSettings;
use yii\console\ExitCode;
use Craft;
use craft\models\Section;
use craft\base\Field;

/**
 * Sections Controller
 */
class SectionsController extends SiteController
{
    public function actionEnableAllOnSite(): int
    {
        $site = $this->getSite();

        if (!$site) {
            $this->stderr('Error: Site not found' . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $sections = Craft::$app->sections->getAllSections();
        $this->stdout("Enabling all sections for: {$site->handle}" . PHP_EOL, Console::FG_GREEN);

        foreach ($sections as $section) {
            $this->sectionsService->enableSectionForSite($section, $site);
        }

        return ExitCode::OK;
    }


    private function handleSaveErrors(Section $section): void
    {
        $errors = $section->getErrors();
        foreach ($errors as $error) {
            Craft::error("Error saving section: {$error}", __METHOD__);
        }
    }
}

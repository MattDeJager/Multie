<?php

namespace boost\multie\console\controllers;

use craft\console\Controller;
use Craft;
use craft\elements\Section;
use craft\models\Site;
use boost\multie\Plugin;
use boost\multie\services\FieldsService;
use boost\multie\services\SectionsService;
use boost\multie\services\SiteService;
use yii\console\ExitCode;

/**
 * Site Controller
 */
class SiteController extends Controller
{

    public ?string $siteHandle = null;
    public ?string $siteName = null;
    public ?string $siteLanguage = 'en-US';
    public ?bool $primary = false;
    public ?bool $translateFields = true;
    public ?bool $enableSections = true;

    protected ?FieldsService $fieldsService;
    protected ?SectionsService $sectionsService;
    protected ?siteService $siteService;

public function __construct($id, $module, $config)
    {
        $this->fieldsService = Plugin::getInstance()->field;
        $this->sectionsService = Plugin::getInstance()->section;
        $this->siteService = Plugin::getInstance()->site;
        parent::__construct($id, $module, $config);
    }

    public function options($actionID): array
    {
        $options = parent::options($actionID);

        // Always allow a --siteHandle flag:
        $options[] = 'siteHandle';

        return $options;
    }

    public function actionMakeSite(): int
    {
        $this->siteName = $this->prompt('Enter the site name:', [
            'required' => true,
        ]);

        // Prompt the user for the site handle
        $defaultHandle = $this->formatHandle($this->siteName);
        $this->siteHandle = $this->formatHandle($this->prompt("Enter the site handle: [default: $defaultHandle}", [
            'required' => false,
        ])) ?? $defaultHandle;

        $this->siteLanguage = $this->prompt('Enter the site language [default: en-US]:', [
            'required' => false,
        ]) ?? 'en-US';

        $this->primary = $this->prompt('Is this the primary site? (y/n) [default: n]:', [
            'required' => false,
        ]) ?? false;

        $this->translateFields = $this->prompt('Translate fields for site? (y/n) [default: y]:', [
            'required' => false,
        ]) ?? true;

        $this->enableSections = $this->prompt('Enable sections for site? (y/n) [default: y]:', [
            'required' => false,
        ]) ?? true;

        $this->stdout("Creating site: {$this->siteName} ({$this->siteHandle})" . PHP_EOL);

        // Create and save the site
        $site = new Site();
        $site->name = $this->siteName;
        $site->handle = $this->siteHandle;
        $site->language = $this->siteLanguage;
        $site->hasUrls = true;
        $site->baseUrl = '@web/' . $this->siteHandle;
        $site->primary = $this->primary;
        $site->groupId = 1;

        try {
            // TODO: Update .env & .env.example
            // TODO: Update ddev config

            Craft::$app->sites->saveSite($site, false);
            $this->stdout("Site created successfully.\n");
            if ($this->translateFields) {
                $this->stdout("Translating fields..." . PHP_EOL, Console::FG_GREEN);
                $fields = Craft::$app->fields->getAllFields();
                $this->fieldsService->translateFields($fields);
            }

            if ($this->enableSections) {
                $this->stdout("Enabling all sections for: {$site->handle}" . PHP_EOL, Console::FG_GREEN);
                $sections = Craft::$app->sections->getAllSections();
                $this->sectionsService->enableSectionsForSite($sections, $site);
            }

            return ExitCode::OK;
        } catch (\Throwable $e) {
            $this->stderr($e->getMessage());
            return ExitCode::UNSPECIFIED_ERROR;
        }

    }

    protected function getSite(): ?\craft\models\Site
    {

        return Craft::$app->sites->getSiteByHandle($this->siteHandle);

    }


    private function formatHandle(?string $handle): ?string
    {
        if (!$handle) {
            return null;
        }

        // Step 1: Trim whitespace and convert to lowercase
        $handle = trim(strtolower($handle));

        // Step 2: Capitalize the first letter of each word
        $handle = ucwords($handle);

        // Step 3: Remove spaces and convert the first character to lowercase
        $handle = str_replace(' ', '', $handle);

        // Step 4: Ensure the first character is lowercase
        return lcfirst($handle);
    }


}
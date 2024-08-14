<?php

namespace boost\multie\services;

use Craft;
use craft\base\Field;
use craft\errors\EntryTypeNotFoundException;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\models\Site;

class SectionsService
{

    public function copySettingsFromSite($settings, $sectionIds, Site $siteToCopy, Site $site): void
    {
        $sectionsService = Craft::$app->sections;

        foreach ($sectionIds as $sectionId) {
            $section = $sectionsService->getSectionById($sectionId);
            if (!$section) {
                Craft::error("Section not found for ID: {$sectionId}", __METHOD__);
                continue;
            }

            $sectionSiteSettings = $section->getSiteSettings();
            $siteToCopySettings = $sectionSiteSettings[$siteToCopy->id] ?? null;

            if (!$siteToCopySettings) {
                Craft::error("Site settings not found for site: {$siteToCopy->id}", __METHOD__);
                continue;
            }

            $siteSettings = $sectionSiteSettings[$site->id] ?? new Section_SiteSettings();
            foreach ($settings as $setting) {
                $siteSettings[$setting] = $siteToCopySettings[$setting];
            }

            $sectionSiteSettings[$site->id] = $siteSettings;
            $section->setSiteSettings($sectionSiteSettings);

            try {
                $sectionsService->saveSection($section);
            } catch (\Throwable $e) {
                Craft::error("Error saving section: {$e->getMessage()}", __METHOD__);
            }
        }

    }

    public function updateSectionsStatusForSite($sectionIds, $status, Site $site): void
    {
        foreach ($sectionIds as $sectionId) {
            $section = Craft::$app->sections->getSectionById($sectionId);
            if (!$section) {
                Craft::error("Section not found: {$sectionId}", __METHOD__);
                continue;
            }

            if ($status == 'enabled') {
                $this->enableSectionForSite($section, $site);
            } else {
                $this->disableSectionForSite($section, $site);
            }
        }
    }

    public function enableSectionsForSite($sections, Site $site): void
    {
        foreach ($sections as $section) {
            $this->enableSectionForSite($section, $site);
        }
    }

    private function disableSectionForSite(Section $section, Site $site): void
    {
        $sectionSiteSettings = $section->getSiteSettings();
        $siteSettings = $sectionSiteSettings[$site->id] ?? null;
        if ($siteSettings) {
            unset($sectionSiteSettings[$site->id]);
            $section->setSiteSettings($sectionSiteSettings);
            Craft::$app->sections->saveSection($section);
        }

    }


    public function enableSectionForSite(Section $section, Site $site): void
    {
        // Enable title translation method for single section entry types
        if ($section->type == Section::TYPE_SINGLE) {
            $this->updateEntryTypesTitleTranslation($section);
        }

        // Update or create site settings for the section
        $sectionSiteSettings = $section->getSiteSettings();
        $siteSettings = $sectionSiteSettings[$site->id] ?? new Section_SiteSettings();
        $siteSettings->siteId = $site->id;
        $siteSettings->enabledByDefault = true;

        $sectionSiteSettings[$site->id] = $siteSettings;
        $section->setSiteSettings($sectionSiteSettings);

        // Save the section
        try {
            Craft::$app->sections->saveSection($section);
        } catch (\Throwable $e) {
            Craft::error("Error saving section: {$e->getMessage()}", __METHOD__);
        }
    }

    private function updateEntryTypesTitleTranslation(Section $section): void
    {
        foreach ($section->getEntryTypes() as $entryType) {
            $entryType->titleTranslationMethod = Field::TRANSLATION_METHOD_SITE;

            try {
                Craft::$app->sections->saveEntryType($entryType);
            } catch (EntryTypeNotFoundException $e) {
                Craft::error("Entry type not found: {$e->getMessage()}", __METHOD__);
            } catch (\Throwable $e) {
                Craft::error("Error saving entry type: {$e->getMessage()}", __METHOD__);
            }
        }
    }
}
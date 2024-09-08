<?php

namespace boost\multie\services;

use Craft;
use craft\base\Field;
use craft\errors\EntryTypeNotFoundException;
use craft\models\EntryType;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\models\Site;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class SectionsService
{
    public function copySectionSettingsFromSite($settings, $sectionIds, Site $siteToCopy, Site $site): void
    {
        $sectionsService = Craft::$app->sections;

        $this->processSections($sectionIds, function (Section $section) use ($settings, $siteToCopy, $site, $sectionsService) {

            $sectionSiteSettings = $section->getSiteSettings();
            $siteToCopySettings = $sectionSiteSettings[$siteToCopy->id] ?? null;

            if (!$siteToCopySettings) {
                Craft::error("Site settings not found for site: {$siteToCopy->id}", __METHOD__);
                return;
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
        });

    }

    public function updateSectionsStatusForSite($sectionIds, $status, Site $site): void
    {
        $this->processSections($sectionIds, function (Section $section) use ($status, $site) {
            if ($status == 'enabled') {
                $this->enableSectionForSite($section, $site);
            } else {
                $this->disableSectionForSite($section, $site);
            }
        });
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

    public function updateAllEntryTypesForSections(array $sectionIds, array $fields): void
    {
        $this->processSections($sectionIds, function (Section $section) use ($fields) {
            $this->updateAllEntryTypesForSection($section, $fields);
        });
    }

    public function updatePropagationMethodForSections(mixed $sectionIds, mixed $propagationMethod, Site $site): void
    {
        $this->processSections($sectionIds, function (Section $section) use ($propagationMethod, $site) {
            $this->updatePropagationMethodForSection($section, $propagationMethod);
        });
    }

    private function processSections(array $sectionIds, callable $callback): void
    {
        foreach ($sectionIds as $section) {
            $section = Craft::$app->sections->getSectionById($section);
            if (!$section) {
                Craft::error("Section not found: {$section}", __METHOD__);
                continue;
            }
            $callback($section);
        }

    }

    public function updateAllEntryTypesForSection(Section $section, array $fields): void
    {
        foreach ($section->getEntryTypes() as $entryType) {
            $this->updateEntryTypeFields($entryType, $fields);
            $this->saveEntryType($entryType);
        }
    }

    private function updateEntryTypeFields(EntryType $entryType, array $fields): void
    {
        foreach ($fields as $field) {
            $entryType->{$field['handle']} = $field['value'];
        }
    }

    private function saveEntryType(EntryType $entryType): void
    {
        try {
            Craft::$app->sections->saveEntryType($entryType);
        } catch (EntryTypeNotFoundException $e) {
            Craft::error("Entry type not found: {$e->getMessage()}", __METHOD__ );
        } catch (\Throwable $e) {
            Craft::error("Error saving entry type: {$e->getMessage()}", __METHOD__ );
        }
    }

    private function updatePropagationMethodForSection(Section $section, mixed $propagationMethod): void
    {
        $section->propagationMethod = $propagationMethod;
        try {
            Craft::$app->sections->saveSection($section);
        } catch (\Throwable $e) {
            Craft::error("Error saving section: {$e->getMessage()}", __METHOD__);
        }
    }


}
<?php

namespace boost\multie\services;

use Craft;

class EntryTypeService
{
    // TODO: This is exact same function is used inside the FieldsService -> Refactor to extend a common class or Interface
    public function updateEntryTypes($ids, $properties): void
    {
        foreach ($ids as $id) {
            $entryType = Craft::$app->entries->getEntryTypeById($id);
            if (!$entryType) {
                Craft::error("Entry type not found: {$id}", __METHOD__);
                continue;
            }
            foreach ($properties as $property) {
                if (property_exists($entryType, $property['handle'])) {
                    $entryType->{$property['handle']} = $property['value'];
                }
            }
            try {
                Craft::$app->entries->saveEntryType($entryType);
            } catch (\Throwable $e) {
                Craft::error("Error saving Entry Type: {$e->getMessage()}", __METHOD__);
            }

        }
    }


}
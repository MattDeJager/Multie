<?php

namespace boost\multie\services;

use boost\multie\helpers\ElementUpdater;
use Craft;

class EntryTypeService
{
    // TODO: This is exact same function is used inside the FieldsService -> Refactor to extend a common class or Interface
    public function updateEntryTypes($ids, $properties): void
    {
        ElementUpdater::update(
            $ids,
            $properties,
            fn($id) => Craft::$app->entries->getEntryTypeById($id),
            fn($entryType) => Craft::$app->entries->saveEntryType($entryType)
        );
    }


}
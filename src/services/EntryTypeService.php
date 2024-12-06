<?php

namespace boost\multie\services;

use boost\multie\helpers\ElementUpdater;
use Craft;

class EntryTypeService
{
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
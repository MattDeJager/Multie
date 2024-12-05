<?php

namespace boost\multie\services;

use boost\multie\helpers\ElementUpdater;
use Craft;
use boost\multie\models\FieldGroup;

class FieldsService
{
    public function updateFields($fieldIds, $fieldProperties): void
    {
        ElementUpdater::update(
            $fieldIds,
            $fieldProperties,
            fn($id) => Craft::$app->fields->getFieldById($id),
            fn($field) => Craft::$app->fields->saveField($field)
        );
    }

    public function getFieldsInGroup(?FieldGroup $fieldGroup): array
    {
        if (!$fieldGroup) {
            return Craft::$app->fields->getAllFields();
        }

        $fields = [];
        foreach ($fieldGroup->getFieldTypes() as $type) {
            $fields = array_merge($fields, Craft::$app->fields->getFieldsByType($type));
        }
        return $fields;
    }


}
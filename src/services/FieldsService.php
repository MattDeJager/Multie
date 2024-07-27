<?php

namespace matthewdejager\craftmultie\services;

use Craft;
use matthewdejager\craftmultie\models\FieldGroup;

class FieldsService
{
    public function updateFields($fieldIds, $fieldProperties): void
    {
        foreach ($fieldIds as $fieldId) {
            $field = Craft::$app->fields->getFieldById($fieldId);
            if (!$field) {
                Craft::error("Field not found: {$fieldId}", __METHOD__);
                continue;
            }
            foreach ($fieldProperties as $fieldProperty) {
                if (property_exists($field, $fieldProperty['handle'])) {
                    $field->{$fieldProperty['handle']} = $fieldProperty['value'];
                }
            }
            try {
                Craft::$app->fields->saveField($field);
            } catch (\Throwable $e) {
                Craft::error("Error saving field: {$e->getMessage()}", __METHOD__);
            }

        }
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
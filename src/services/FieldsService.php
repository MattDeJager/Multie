<?php

namespace matthewdejager\craftmultie\services;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;
use craft\helpers\Console;
use yii\base\Component;

class FieldsService
{

    public function translateFields($fields, $config = []): void
    {
        foreach ($fields as $field) {
            try {
                $this->configureField($field, $config);
            } catch (\Throwable $e) {

            }
        }
    }

    /**
     * Configures the field
     *
     * @param FieldInterface $field
     * @return void
     * @throws \Throwable
     */
    public function configureField(FieldInterface $field, $config = []): ?bool
    {

        if ($field instanceof Matrix) {
            $this->configureMatrixField($field, $config);
        } else if ($field instanceof BaseRelationField) {
            $this->configureBaseRelationField($field, $config);
        } else {
            $this->configureSimpleField($field, $config);
        }
        try {
           return Craft::$app->fields->saveField($field);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function configureMatrixField(Matrix $field, $config): void
    {
        $field->propagationMethod = $config['propagationMethod'];
    }

    private function configureBaseRelationField(BaseRelationField $field, $config): void
    {
        $field->localizeRelations = $config['localizeRelations'];
    }

    private function configureSimpleField(FieldInterface $field, $config): void
    {
        $field->translationMethod = $config['translationMethod'];
    }

}
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

    public function translateFields($fields): void
    {
        foreach ($fields as $field) {
            try {
                $this->configureField($field);
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
    public function configureField(FieldInterface $field): ?bool
    {

        if ($field instanceof Matrix) {
            $this->configureMatrixField($field);
        } else if ($field instanceof BaseRelationField) {
            $this->configureBaseRelationField($field);
        } else {
            $this->configureSimpleField($field);
        }
        try {
           return Craft::$app->fields->saveField($field);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function configureMatrixField(Matrix $field): void
    {
        $field->propagationMethod = Matrix::PROPAGATION_METHOD_NONE;
    }

    private function configureBaseRelationField(BaseRelationField $field): void
    {
        // localizeRelation
        $field->localizeRelations = true;
    }

    private function configureSimpleField(FieldInterface $field): void
    {
        $field->translationMethod = Field::TRANSLATION_METHOD_SITE;
    }

}
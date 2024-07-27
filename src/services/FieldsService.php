<?php

namespace matthewdejager\craftmultie\services;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\fields\Assets as AssetsField;
use craft\fields\BaseRelationField;
use craft\fields\Categories as CategoriesField;
use craft\fields\Checkboxes;
use craft\fields\Color;
use craft\fields\Country;
use craft\fields\Date;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Entries as EntriesField;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\Matrix as MatrixField;
use craft\fields\Money;
use craft\fields\MultiSelect;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Table as TableField;
use craft\fields\Tags as TagsField;
use craft\fields\Time;
use craft\fields\Url;
use craft\fields\Users as UsersField;
use craft\helpers\Console;
use matthewdejager\craftmultie\models\FieldGroup;
use yii\base\Component;

class FieldsService
{
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
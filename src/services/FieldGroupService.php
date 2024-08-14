<?php

namespace boost\multie\services;

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
use boost\multie\models\FieldGroup;


/**
 * FIELD TYPES
 * AssetsField::class,
 * CategoriesField::class,
 * Checkboxes::class,
 * Color::class,
 * Country::class,
 * Date::class,
 * Dropdown::class,
 * Email::class,
 * EntriesField::class,
 * Lightswitch::class,
 * MatrixField::class,
 * Money::class,
 * MultiSelect::class,
 * Number::class,
 * PlainText::class,
 * RadioButtons::class,
 * TableField::class,
 * TagsField::class,
 * Time::class,
 * Url::class,
 * UsersField::class,
 *
 */
class FieldGroupService
{
    public const SIMPLE_FIELDS = 1;
    public const BASE_RELATION_FIELDS = 2;
    public const MATRIX_FIELDS = 3;

    private const FIELD_GROUP_CONFIG = [
        [
            'id' => self::SIMPLE_FIELDS,
            'name' => 'Simple Fields',
            'fieldTypes' => [
                Checkboxes::class,
                Color::class,
                Country::class,
                Date::class,
                Dropdown::class,
                Email::class,
                Lightswitch::class,
                Money::class,
                MultiSelect::class,
                Number::class,
                PlainText::class,
                RadioButtons::class,
                TableField::class,
                TagsField::class,
                Time::class,
                Url::class,
                UsersField::class,
            ],
        ],
        [
            'id' => self::BASE_RELATION_FIELDS,
            'name' => 'Base Relations Fields',
            'fieldTypes' => [
                BaseRelationField::class,
            ],
        ],
        [
            'id' => self::MATRIX_FIELDS,
            'name' => 'Matrix Fields',
            'fieldTypes' => [
                MatrixField::class,
            ],
        ],
    ];

    public function getAllFieldGroups(): array
    {
        $fieldGroups = [];

        foreach (self::FIELD_GROUP_CONFIG as $config) {
            $fieldGroup = new FieldGroup();
            $fieldGroup->id = $config['id'];
            $fieldGroup->name = $config['name'];
            $fieldGroup->setFieldTypes($config['fieldTypes']);

            $fieldGroups[] = $fieldGroup;
        }

        return $fieldGroups;
    }

    public function getFieldGroupById(?int $id): ?FieldGroup
    {
        foreach ($this->getAllFieldGroups() as $fieldGroup) {
            if ($fieldGroup->id === $id) {
                return $fieldGroup;
            }
        }

        return null;
    }


}
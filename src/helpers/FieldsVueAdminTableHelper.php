<?php

namespace boost\multie\helpers;

use boost\multie\controllers\FieldsController;
use boost\multie\controllers\SectionsController;
use boost\multie\models\FieldGroup;
use boost\multie\services\FieldGroupService;
use Craft;
use craft\base\FieldInterface;
use craft\fields\MissingField;
use craft\helpers\Cp;
use craft\helpers\UrlHelper;

class FieldsVueAdminTableHelper extends VueAdminTableHelper
{
    public static function actions(FieldGroup $fieldGroup = null): array
    {
        $fieldTranslationMethod = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions(FieldsController::ACTION_UPDATE, 'translationMethod'),
            'translate'
        );

        $fieldPropagationMethod = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Propagation Method'), [
            VueAdminTableHelper::getActionArray('Only save blocks to the site they were created in', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'propagationMethod', 'value' => 'none']]),
            VueAdminTableHelper::getActionArray('Save blocks to other sites in the same site group', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'propagationMethod', 'value' => 'siteGroup']]),
            VueAdminTableHelper::getActionArray('Save blocks to other sites with the same language', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'propagationMethod', 'value' => 'language']]),
            VueAdminTableHelper::getActionArray('Save blocks to all sites the owner element is saved in', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'propagationMethod', 'value' => 'all']]),
            VueAdminTableHelper::getActionArray('Custom...', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'propagationMethod', 'value' => 'custom']]),
        ]);

        $fieldManageRelations = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Manage relations on a per-site basis'), [
            VueAdminTableHelper::getActionArray('Enable', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'localizeRelations', 'value' => true]], 'enabled'),
            VueAdminTableHelper::getActionArray('Disable', FieldsController::ACTION_UPDATE, 'fields', [['handle' => 'localizeRelations', 'value' => false]], 'disabled'),
        ]);

        if ($fieldGroup instanceof FieldGroup) {
            switch ($fieldGroup->id) {
                case FieldGroupService::SIMPLE_FIELDS:
                    return [
                        $fieldTranslationMethod
                    ];
                case FieldGroupService::BASE_RELATION_FIELDS:
                    return [
                        $fieldManageRelations,
                    ];
                case FieldGroupService::MATRIX_FIELDS:
                    return [
                        $fieldPropagationMethod
                    ];
            }
        }
        return [
            $fieldTranslationMethod,
            $fieldPropagationMethod,
            $fieldManageRelations,
        ];
    }

    public static function data($entries): array
    {

        $tableData = array_map(function (FieldInterface $field) {
            return [
                'id' => $field->id,
                'title' => Craft::t('site', $field->name),
                'translatable' => $field->getIsTranslatable(null) ? ($field->getTranslationDescription(null) ?? Craft::t('app', 'This field is translatable.')) : false,
                'searchable' => $field->searchable ? true : false,
                'url' => UrlHelper::url('settings/fields/edit/' . $field->id),
                'handle' => $field->handle,
                'type' => [
                    'isMissing' => $field instanceof MissingField,
                    'label' => $field instanceof MissingField ? $field->expectedType : $field->displayName(),
                    'icon' => Cp::iconSvg($field::icon()),
                ],
            ];
        }, $entries);

        return $tableData;
    }

    public static function columns(): array
    {
        return [];
    }


}
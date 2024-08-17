<?php

namespace boost\multie\helpers;

use boost\multie\controllers\SectionsController;
use boost\multie\models\FieldGroup;
use boost\multie\services\FieldGroupService;
use Craft;
use craft\helpers\UrlHelper;

class FieldsVueAdminTableHelper extends VueAdminTableHelper
{
    public static function actions(FieldGroup $fieldGroup = null): array
    {
        $fieldTranslationMethod = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Translation Method'),
            VueAdminTableHelper::getTranslationMethodActions('multie/fields/update', 'translationMethod'),
            'translate'
        );

        $fieldPropagationMethod = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Propagation Method'), [
            VueAdminTableHelper::getActionArray('Only save blocks to the site they were created in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'none']]),
            VueAdminTableHelper::getActionArray('Save blocks to other sites in the same site group', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'siteGroup']]),
            VueAdminTableHelper::getActionArray('Save blocks to other sites with the same language', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'language']]),
            VueAdminTableHelper::getActionArray('Save blocks to all sites the owner element is saved in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'all']]),
            VueAdminTableHelper::getActionArray('Custom...', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'custom']]),
        ]);

        $fieldManageRelations = VueAdminTableHelper::getActionsArray(\Craft::t('app', 'Manage relations on a per-site basis'), [
            VueAdminTableHelper::getActionArray('Enable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => true]], 'enabled'),
            VueAdminTableHelper::getActionArray('Disable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => false]], 'disabled'),
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


        $tableData = array_map(function ($field) {
            return [
                'id' => $field->id,
                'title' => Craft::t('site', $field->name),
                'translatable' => $field->getIsTranslatable() ? ($field->getTranslationDescription() ?? Craft::t('app', 'This field is translatable.')) : false,
                'searchable' => $field->searchable ? true : false,
                'url' => UrlHelper::url('settings/fields/edit/' . $field->id),
                'handle' => $field->handle,
                'type' => [
                    'isMissing' => false,
                    'label' => $field->displayName()
                ],
                'group' => $field->group ? $field->group->name : "<span class=\"error\">" . Craft::t('app', '(Ungrouped)') . "</span>",
            ];
        }, $entries);

        return $tableData;
    }

    public static function columns(): array
    {
        return [];
    }


}
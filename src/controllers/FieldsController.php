<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\helpers\VueAdminTableHelper;
use matthewdejager\craftmultie\models\FieldGroup;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\FieldGroupService;
use matthewdejager\craftmultie\services\FieldsService;

class FieldsController extends Controller
{
    public function actionIndex(int $fieldGroupId = null): \yii\web\Response
    {
        $this->requireAdmin();

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;
        /** @var FieldGroupService $fieldGroupService */
        $fieldGroupService = Plugin::getInstance()->fieldGroup;

        $fieldGroup = $fieldGroupService->getFieldGroupById($fieldGroupId);
        $fieldGroups = $fieldGroupService->getAllFieldGroups();
        $actions = $this->getTableActionsForFieldGroup($fieldGroup);
        $fields = $fieldService->getFieldsInGroup($fieldGroup);

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
        }, $fields);

        $field = Craft::$app->fields->getFieldById(1);

        return $this->renderTemplate('multie/fields/index.twig', [
            "field" => $field,
            "fieldGroups" => $fieldGroups,
            "actions" => $actions,
            "tableData" => $tableData,
        ]);
    }

    public function actionUpdate(): \yii\web\Response
    {
        $this->requireAdmin();
        $fieldIds = Craft::$app->request->post("ids");
        $fieldConfig = json_decode(Craft::$app->request->getBodyParam('fields'), true);
        $fieldService = Plugin::getInstance()->field;

        $fieldService->updateFields($fieldIds, $fieldConfig);

        // Get the referrer URL
        $referrer = Craft::$app->request->referrer;

        // Redirect to the referrer URL if it exists, otherwise redirect to a default URL
        return $this->redirect($referrer ?: 'multie/fields');
    }


    private function getTableActionsForFieldGroup(?FieldGroup $group): array
    {
        $fieldTranslationMethod = [
            'label' => \Craft::t('app', 'Translation Method'),
            'icon' => 'translate',
            'actions' => [
                VueAdminTableHelper::getActionArray('Not translatable', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'none']]),
                VueAdminTableHelper::getActionArray('Translate for each site', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'site']]),
                VueAdminTableHelper::getActionArray('Translate for each site group', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'siteGroup']]),
                VueAdminTableHelper::getActionArray('Translate for each language', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'language']]),
                VueAdminTableHelper::getActionArray('Custom…', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'custom']]),
            ],
        ];

        $fieldPropagationMethod =   [
            'label' => \Craft::t('app', 'Propagation Method'),
            'actions' => [
                VueAdminTableHelper::getActionArray('Only save blocks to the site they were created in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'none']]),
                VueAdminTableHelper::getActionArray('Save blocks to other sites in the same site group', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'siteGroup']]),
                VueAdminTableHelper::getActionArray('Save blocks to other sites with the same language', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'language']]),
                VueAdminTableHelper::getActionArray('Save blocks to all sites the owner element is saved in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'all']]),
                VueAdminTableHelper::getActionArray('Custom...', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'custom']]),
            ],
        ];

        $fieldManageRelations = [
            'label' => \Craft::t('app', 'Manage relations on a per-site basis'),
            'actions' => [
                VueAdminTableHelper::getActionArray('Enable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => true]], 'enabled'),
                VueAdminTableHelper::getActionArray('Disable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => false]], 'disabled'),
            ],
        ];

        if ($group instanceof FieldGroup) {
            switch ($group->id) {
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
}

<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\helpers\VueAdminTableHelper;
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
        $actions = $this->getTableActions();
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
        dd(json_decode(Craft::$app->request->getBodyParam('fields'), true));
    }

    public function actionUpdateAll(): \yii\web\Response
    {
        $this->requireAdmin();

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;

        $config = [
            'translationMethod' => Craft::$app->request->getBodyParam('translationMethod'),
            'propagationMethod' => Craft::$app->request->getBodyParam('propagationMethod'),
            'localizeRelations' => Craft::$app->request->getBodyParam('localizeRelations'),
        ];

        $fieldService->translateFields(Craft::$app->fields->getAllFields(), $config);

        return $this->renderTemplate('multie/fields/index.twig', [
            "field" => Craft::$app->fields->getFieldById(1),
        ]);
    }

    private function getTableActions(): array
    {
        return [
            [
                'label' => \Craft::t('app', 'Translation Method'),
                'icon' => 'translate',
                'actions' => [
                    VueAdminTableHelper::getActionArray('Not translatable', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'none']]),
                    VueAdminTableHelper::getActionArray('Translate for each site', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'site']]),
                    VueAdminTableHelper::getActionArray('Translate for each site group', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'siteGroup']]),
                    VueAdminTableHelper::getActionArray('Translate for each language', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'language']]),
                    VueAdminTableHelper::getActionArray('Custom…', 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'custom']]),
                ],
            ],
            [
                'label' => \Craft::t('app', 'Propagation Method'),
                'actions' => [
                    VueAdminTableHelper::getActionArray('Only save blocks to the site they were created in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'none']]),
                    VueAdminTableHelper::getActionArray('Save blocks to other sites in the same site group', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'siteGroup']]),
                    VueAdminTableHelper::getActionArray('Save blocks to other sites with the same language', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'language']]),
                    VueAdminTableHelper::getActionArray('Save blocks to all sites the owner element is saved in', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'all']]),
                    VueAdminTableHelper::getActionArray('Custom...', 'multie/fields/update', 'fields', [['handle' => 'propagationMethod', 'value' => 'custom']]),
                ],
            ],
            [
                'label' => \Craft::t('app', 'Manage relations on a per-site basis'),
                'actions' => [
                    VueAdminTableHelper::getActionArray('Enable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => true]]),
                    VueAdminTableHelper::getActionArray('Disable', 'multie/fields/update', 'fields', [['handle' => 'localizeRelations', 'value' => false]]),
                ],
            ],
        ];
    }
}

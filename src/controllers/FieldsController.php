<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\models\FieldGroup;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\FieldGroupService;
use matthewdejager\craftmultie\services\FieldsService;
use matthewdejager\craftmultie\services\SectionsService;
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
use yii\base\Component;

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

        $tableData = [];

        foreach ($fields as $field) {
            $tableData[] = [
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
                'group' => $field->group ? $field->group->name : "<span class=\"error\">#{'(Ungrouped)'|t('app')}</span>",

            ];
        }

        $field = Craft::$app->fields->getFieldById(1);

        return $this->renderTemplate('multie/fields/index.twig', [
            "field" => $field,
            "fieldGroups" => $fieldGroups,
            "actions" => $actions,
            "tableData" => $tableData
        ]);
    }

    public function actionUpdateAll(): \yii\web\Response
    {
        $this->requireAdmin();

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;

        $translationMethod = Craft::$app->request->getBodyParam('translationMethod');
        $propagationMethod = Craft::$app->request->getBodyParam('propagationMethod');
        $localizeRelations = Craft::$app->request->getBodyParam('localizeRelations');

        $fields = Craft::$app->fields->getAllFields();

        $config = [
            'translationMethod' => $translationMethod,
            'propagationMethod' => $propagationMethod,
            'localizeRelations' => $localizeRelations
        ];

        $fieldService->translateFields($fields, $config);


        $field = Craft::$app->fields->getFieldById(1);


        return $this->renderTemplate('multie/fields/index.twig', ["field" => $field]);
    }

    private function getTableActions()

        // TODO all the below actions should be the same with a different config/fields value
    {
        return [
            [
                'label' => \Craft::t('app', 'Translation Method'),
                'icon' => 'translate', // TODO set a icon here
                'actions' => [
                    [
                        'label' => \Craft::t('app', 'Not translatable'),
                        'action' => 'multie/fields/update-translation-method',
                        'param' => 'translationMethod',
                        'value' => 'none',
                    ],
                    [
                        'label' => \Craft::t('app', 'Translate for each site'),
                        'action' => 'multie/fields/update-translation-method',
                        'param' => 'translationMethod',
                        'value' => 'site',
                    ],
                    [
                        'label' => \Craft::t('app', 'Translate for each site group'),
                        'action' => 'multie/fields/update-translation-method',
                        'param' => 'translationMethod',
                        'value' => 'siteGroup',
                    ],
                    [
                        'label' => \Craft::t('app', 'Translate for each language'),
                        'action' => 'multie/fields/update-translation-method',
                        'param' => 'translationMethod',
                        'value' => 'language',
                    ],
                    [
                        'label' => \Craft::t('app', 'Custom…'),
                        'action' => 'multie/fields/update-translation-method',
                        'param' => 'translationMethod',
                        'value' => 'custom',
                    ],
                ],
            ],

            [
                "label" => Craft::t('app', 'Propagation Method'),
                'actions' => [
                    [
                        'label' => \Craft::t('app', 'Only save blocks to the site they were created in'),
                        'action' => 'multie/fields/update-propagation-method',
                        'param' => 'propagationMethod',
                        'value' => 'none',
                    ],
                    [
                        'label' => \Craft::t('app', 'Save blocks to other sites in the same site group'),
                        'action' => 'multie/fields/update-propagation-method',
                        'param' => 'propagationMethod',
                        'value' => 'siteGroup',
                    ],
                    [
                        'label' => \Craft::t('app', 'Save blocks to other sites with the same language'),
                        'action' => 'multie/fields/update-propagation-method',
                        'param' => 'propagationMethod',
                        'value' => 'language',
                    ],
                    [
                        'label' => \Craft::t('app', 'Save blocks to all sites the owner element is saved in'),
                        'action' => 'multie/fields/update-propagation-method',
                        'param' => 'propagationMethod',
                        'value' => 'all',
                    ],
                    [
                        'label' => \Craft::t('app', 'Custom...'),
                        'action' => 'multie/fields/update-propagation-method',
                        'param' => 'propagationMethod',
                        'value' => 'custom',
                    ],
                ],
            ],
            [
                "label" => Craft::t('app', 'Manage relations on a per-site basis'),
                'actions' => [
                    [
                        'label' => \Craft::t('app', 'Enable'),
                        'action' => 'multie/fields/update-localize-relations',
                        'param' => 'localizeRelations',
                        'value' => true,
                        'status' => 'enabled'
                    ],
                    [
                        'label' => \Craft::t('app', 'Disable'),
                        'action' => 'multie/fields/update-localize-relations',
                        'param' => 'localizeRelations',
                        'value' => false,
                        'status' => 'disabled'
                    ],
                ],
            ],
        ];
    }


}
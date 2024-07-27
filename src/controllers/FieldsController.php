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

        // todo: Connect Fields in Table to Fields in Side Bar
        // todo: Add dynamic actions for each field group
        $this->requireAdmin();

        /** @var FieldsService $fieldService */
        $fieldService = Plugin::getInstance()->field;
        /** @var FieldGroupService $fieldGroupService */
        $fieldGroupService = Plugin::getInstance()->fieldGroup;

        $fieldGroup = $fieldGroupService->getFieldGroupById($fieldGroupId);

        $fieldGroups = $fieldGroupService->getAllFieldGroups();

        $actions = [
            [
                'label' => \Craft::t('app', 'Set Status'),
                'actions' => [
                    [
                        'label' => \Craft::t('app', 'Enabled'),
                        'action' => 'multie/sections/update-status',
                        'param' => 'status',
                        'value' => 'enabled',
                        'status' => 'enabled',
                    ],
                    [
                        'label' => \Craft::t('app', 'Disabled'),
                        'action' => 'multie/sections/update-status',
                        'param' => 'status',
                        'value' => 'disabled',
                        'status' => 'disabled',
                    ],
                ],
            ],

            [
                "icon" => "settings",
                'actions' => [
                    [
                        'label' => \Craft::t('app', 'Copy settings from default site'),
                        'action' => 'multie/sections/copy-settings',
                        'param' => 'site',
                        'value' => 'default',
                        'icon' => 'settings',
                    ]
                ],
            ],

        ];

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

//        [{
//        id: field.id,
//        title: field.name|t('site'),
//        translatable: field.getIsTranslatable() ? (field.getTranslationDescription() ?? 'This field is translatable.'|t('app')),
//        searchable: field.searchable ? true : false,
//        url: url('settings/fields/edit/' ~ field.id),
//        handle: field.handle,
//        type: {
//            isMissing: fieldIsMissing,
//            label: fieldIsMissing ? field.expectedType : field.displayName()
//        },
//        group: group ? group.name|t('site')|e : "<span class=\"error\">#{'(Ungrouped)'|t('app')}</span>",
//    }]

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


}
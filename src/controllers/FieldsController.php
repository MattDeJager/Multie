<?php

namespace matthewdejager\craftmultie\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use matthewdejager\craftmultie\Plugin;
use matthewdejager\craftmultie\services\SectionsService;

class FieldsController extends Controller
{

    public function actionIndex(): \yii\web\Response
    {
        $this->requireAdmin();

        $actions = [];
        $tableData = [];


//
//        // Get the 'site' parameter from the URL
//        $siteHandle = Craft::$app->request->get('site', 'default'); // Default to 'default' if not provided
//
//        // Get the site object by its handle
//        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
//
//        $tableData = [];
//
//        $sections = Craft::$app->sections->getAllSections();
//
//        foreach ($sections as $section) {
//
//            $sectionSiteSettings = $section->getSiteSettings()[$site->id] ?? null;
//            $status = isset($sectionSiteSettings) ? 1 : 0;
//
//            $tableData[] = [
//                'id' => $section->id,
//                'title' => Craft::t('site', $section->name),
//                'url' => UrlHelper::url('multie/sections/edit/' . $section->id),
//                'name' => htmlspecialchars(Craft::t('site', $section->name)),
//                'status' => $status,
//                'entry_uri_format' => $sectionSiteSettings->uriFormat ?? "", // Set the entry_uri_format field to "test"
//                'template' => $sectionSiteSettings->template ?? "", // Set the template field to "test"
//            ];
//        }
//
//        $actions = [
//            [
//                'label' => \Craft::t('app', 'Set Status'),
//                'actions' => [
//                    [
//                        'label' => \Craft::t('app', 'Enabled'),
//                        'action' => 'multie/sections/update-status',
//                        'param' => 'status',
//                        'value' => 'enabled',
//                        'status' => 'enabled',
//                    ],
//                    [
//                        'label' => \Craft::t('app', 'Disabled'),
//                        'action' => 'multie/sections/update-status',
//                        'param' => 'status',
//                        'value' => 'disabled',
//                        'status' => 'disabled',
//                    ],
//                ],
//            ],
//
//            [
//                "icon" => "settings",
//                'actions' => [
//                    [
//                        'label' => \Craft::t('app', 'Copy settings from default site'),
//                        'action' => 'multie/sections/copy-settings',
//                        'param' => 'site',
//                        'value' => 'default',
//                        'icon' => 'settings',
//                    ]
//                ],
//            ],
//
//        ];

        return $this->renderTemplate('multie/sections/index.twig', [
            "tableData" => $tableData,
            'actions' => $actions,
        ]);
    }

}
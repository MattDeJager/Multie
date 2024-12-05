<?php

namespace boost\multie;

use boost\multie\models\Settings;
use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use yii\base\Event;

/**
 * Multie plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @author Matthew De Jager <matthewdejager5@gmail.com>
 * @copyright Matthew De Jager
 * @license MIT
 */
class Plugin extends BasePlugin
{
    const HANDLE = 'multie';

    const PERMISSION_MANAGE_SETTINGS = 'accessPlugin-'.self::HANDLE;
    const PERMISSION_EDIT_SECTIONS = self::HANDLE . '-editSections';
    const PERMISSION_EDIT_FIELDS = self::HANDLE . '-editFields';
    const PERMISSION_EDIT_ENTRY_TYPES = self::HANDLE . '-editFields';


    /**
     * Returns the base config that the plugin should be instantiated with.
     *
     * It is recommended that plugins define their internal components from here:
     *
     * ```php
     * public static function config(): array
     * {
     *     return [
     *         'components' => [
     *             'myComponent' => ['class' => MyComponent::class],
     *             // ...
     *         ],
     *     ];
     * }
     * ```
     *
     * Doing that enables projects to customize the components as needed, by
     * overriding `\craft\services\Plugins::$pluginConfigs` in `config/app.php`:
     *
     * ```php
     * return [
     *     'components' => [
     *         'plugins' => [
     *             'pluginConfigs' => [
     *                 'my-plugin' => [
     *                     'components' => [
     *                         'myComponent' => [
     *                             'myProperty' => 'foo',
     *                             // ...
     *                         ],
     *                     ],
     *                 ],
     *             ],
     *         ],
     *     ],
     * ];
     * ```
     *
     * The resulting config will be passed to `\Craft::createObject()` to instantiate the plugin.
     *
     * @return array
     */
    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    /**
     * Initializes the module.
     *
     * This method is called after the module is created and initialized with property values
     * given in configuration. The default implementation will initialize [[controllerNamespace]]
     * if it is not set.
     *
     * If you override this method, please make sure you call the parent implementation.
     */
    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });

        // REGISTER SERVICES
        $this->setComponents([
            'field' => \boost\multie\services\FieldsService::class,
            'entryType' => \boost\multie\services\EntryTypeService::class,
            'section' => \boost\multie\services\SectionsService::class,
            'site' => \boost\multie\services\SiteService::class,
            'fieldGroup' => \boost\multie\services\FieldGroupService::class,
        ]);
    }


    private function attachEventHandlers(): void
    {
        // CP NAV
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                if (Craft::$app->getUser()->checkPermission(self::PERMISSION_MANAGE_SETTINGS)) {
                    $event->navItems[] = [
                        'url' => self::HANDLE . '/sections',
                        'label' => 'Multie',
                        'icon' => '@boost/multie/icon-mask.svg',
                        'subnav' => [
                            'sections' => [
                                'label' => 'Sections',
                                'url' => self::HANDLE . '/sections',
                            ],
                            'fields' => [
                                'label' => 'Fields',
                                'url' => self::HANDLE . '/fields',
                            ],
                            'entryTypes' => [
                                'label' => 'Entry Types',
                                'url' => self::HANDLE . '/entry-types',
                            ],
                        ],
                    ];

                }
            }
        );


        if (Craft::$app->getRequest()->getIsCpRequest()) {
            // ROUTES
            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_CP_URL_RULES,
                function (RegisterUrlRulesEvent $event) {
                    $event->rules[self::HANDLE . '/sections'] = self::HANDLE . '/sections/site-settings-index';
                    $event->rules[self::HANDLE . '/sections/<type:(all|channel|single|structure)>'] = self::HANDLE . '/sections/site-settings-index';
                    $event->rules[self::HANDLE . '/sections/general'] = self::HANDLE . '/sections/general-settings-index';
                    $event->rules[self::HANDLE . '/sections/general/<type:(all|channel|single|structure)>'] = self::HANDLE . '/sections/general-settings-index';
                    $event->rules[self::HANDLE . '/fields'] = self::HANDLE . '/fields/index';
                    $event->rules[self::HANDLE . '/fields/<fieldGroupId:\d*>'] = self::HANDLE . '/fields/index';
                    $event->rules[self::HANDLE . '/entry-types'] = self::HANDLE . '/entry-types/index';
                    $event->rules[self::HANDLE . '/translations'] = self::HANDLE . '/translations/index';
                }
            );
        }


        // PERMISSIONS
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                $event->permissions[] = [
                    'heading' => 'Multie',
                    'permissions' => [
                        self::PERMISSION_MANAGE_SETTINGS => [
                            'label' => \Craft::t('app','Manage Multie settings'),
                            'nested' => [
                                self::PERMISSION_EDIT_FIELDS => [
                                    'label' => \Craft::t('app','Bulk edit fields'),
                                ],
                                self::PERMISSION_EDIT_SECTIONS => [
                                    'label' => \Craft::t('app','Bulk edit sections'),
                                ],
                                self::PERMISSION_EDIT_ENTRY_TYPES => [
                                    'label' => \Craft::t('app','Bulk edit entry types'),
                                ],
                            ]
                        ],
                    ],
                ];
            }
        );

    }
}

<?php

namespace boost\multie;

use boost\multie\models\Settings;
use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use yii\base\Event;

// TODO: Continue working towards adding plugin to marketplace https://craftcms.com/knowledge-base/craft-console-organizations
// TODO: Translate section Entry Type Title Translation Method & Slug Translation Method
// TODO: Look into adding bulk translations for category groups
// TODO: Create new sites based on exisitng/new site configurations
// TODO: Look into site group translations
// TODO: Handle custom translation methods
// TODO: Add search/filter to tables
// TODO: Work out how to display the section Entry Type Data


// ******************* PRIORIRTY *******************
// TODO: Sections needs two different views 1. Site Settings 2. General Settings

/**
 * Shortcuts to practice!!!
 * - Go To Declaration: ctrl + B
 * - Next Tab: alt + right
 * - Previous Tab: alt + left
 * - Recent Usages: ctrl + shift + E
 * - Global Search: shift + shift
 * - File Strucutre: ctrl + 0
 * -
 *
 */


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

    /** @var string The plugin’s schema version number */
    public string $schemaVersion = '1.0.0';

    /** @var bool Whether the plugin has a settings page in the control panel */
    public bool $hasCpSettings = true;

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
            'section' => \boost\multie\services\SectionsService::class,
            'site' => \boost\multie\services\SiteService::class,
            'fieldGroup' => \boost\multie\services\FieldGroupService::class,
        ]);
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content block on the settings page.
     *
     * @return string|null The rendered settings HTML
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(self::HANDLE . '/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // CP NAV
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
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
//                        'translations' => [
//                            'label' => 'Translations',
//                            'url' => self::HANDLE . '/translations',
//                        ],
                    ],
                ];
            }
        );
        
        // ROUTES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[self::HANDLE . '/sections'] = self::HANDLE . '/sections/site-settings-index';
                $event->rules[self::HANDLE . '/sections/general'] = self::HANDLE . '/sections/general-settings-index';
                $event->rules[self::HANDLE . '/fields'] = self::HANDLE . '/fields/index';
                $event->rules[self::HANDLE . '/fields/<fieldGroupId:\d*>'] = self::HANDLE . '/fields/index';
                $event->rules[self::HANDLE . '/translations'] = self::HANDLE . '/translations/index';
            }
        );

    }
}

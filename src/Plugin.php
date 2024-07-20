<?php

namespace matthewdejager\craftmultie;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use matthewdejager\craftmultie\models\Settings;
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
            // ...
        });

        // REGISTER SERVICES
        $this->setComponents([
            'field' => \matthewdejager\craftmultie\services\FieldsService::class,
            'section' => \matthewdejager\craftmultie\services\SectionsService::class,
            'site' => \matthewdejager\craftmultie\services\SiteService::class,
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
        return Craft::$app->view->renderTemplate('multie/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                $event->navItems[] = [
                    'url' => 'multie',
                    'label' => 'Multie',
                    'icon' => 'multie/icon.svg',
                    'subnav' => [
                        'sites' => [
                            'label' => 'Sites',
                            'url' => 'multie',
                        ],
                        'settings' => [
                            'label' => 'Settings',
                            'url' => 'multie/settings',
                        ],
                    ],
                ];
            }
        );
        
        
        // ROUTES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['multie'] = 'multie/site/index';
            }
        );

    }
}

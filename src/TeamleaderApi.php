<?php
/**
 * Teamleader plugin for Craft CMS 3.x
 *
 * A connection between craft commerce and teamleader to have an easy flow of facturation
 *
 * @link      https://knapen.dev
 * @copyright Copyright (c) 2020 Kjell Knapen
 */

namespace marbles\teamleader;

use marbles\teamleader\services\TeamleaderApi as TeamleaderService;
use marbles\teamleader\models\Settings;
use marbles\teamleader\assetbundles\Teamleader\TeamleaderAsset;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\commerce\elements\Order;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Kjell Knapen
 * @package   Teamleader
 * @since     1.0.0
 *
 * @property  TeamleaderService $teamleader
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class TeamleaderApi extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * TeamleaderApi::$plugin
     *
     * @var TeamleaderApi
     */
    public static $plugin;


    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * TeamleaderApi::$connection
     *
     * @var \Teamleader\Connection
     */
    public static $connection;

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * TeamleaderApi::$client
     *
     * @var \Teamleader\Client
     */
    public static $client;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Teamleader::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        self::$connection = new \Teamleader\Connection();

        // Set connection variables
        self::$connection->setClientId($this->getSettings()->clientId);
        self::$connection->setClientSecret($this->getSettings()->clientSecret);
        self::$connection->setRedirectUrl($this->getSettings()->baseUrl . '/admin/teamleader/connect/integration');

        self::$client = new \Teamleader\Client(self::$connection);


        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Craft::$app->getView()->registerAssetBundle(TeamleaderAsset::class);
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['teamleader'] = 'teamleader/teamleader/index';
                $event->rules['teamleader/settings'] = 'teamleader/teamleader/settings';
                $event->rules['teamleader/connect'] = 'teamleader/connect/index';
                $event->rules['teamleader/connect/integration'] = 'teamleader/connect/set-connection';
            }
        );

        Event::on(Order::class, Order::EVENT_AFTER_COMPLETE_ORDER, function(Event $e) {
            // @var Order $order
            $order = $e->sender;

            $company = $this->teamleader->createCompany($order);

            $invoice = $this->teamleader->createInvoice($order, $company);
        });


        Craft::info(
            Craft::t(
                'teamleader',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    public function getCpNavItem()
    {
        $navItem = parent::getCpNavItem();

        $navItem['label'] = 'Teamleader';

        $navItem['subnav'] = [
            'general' => [
                'label' => Craft::t('teamleader', 'General'),
                'url' => 'teamleader'
            ],
            'connect' => [
                'label' => Craft::t('teamleader', 'Connect'),
                'url' => 'teamleader/connect'
            ],
            'settings' => [
                'label' => Craft::t('teamleader', 'Settings'),
                'url' => 'teamleader/settings'
            ],
        ];

        return $navItem;
    }
    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'teamleader/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}

<?php
/**
 * Teamleader plugin for Craft CMS 3.x
 *
 * A connection between craft commerce and teamleader to have an easy flow of facturation
 *
 * @link      https://knapen.dev
 * @copyright Copyright (c) 2020 Kjell Knapen
 */

namespace kjellknapencraftteamleader\teamleader\services;

use kjellknapencraftteamleader\teamleader\Teamleader;

use Craft;
use craft\base\Component;

/**
 * Teamleader Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Kjell Knapen
 * @package   Teamleader
 * @since     1.0.0
 */
class Teamleader extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Teamleader::$plugin->teamleader->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Teamleader::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}

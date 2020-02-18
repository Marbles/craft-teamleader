<?php
/**
 * Teamleader plugin for Craft CMS 3.x
 *
 * A connection between craft commerce and teamleader to have an easy flow of facturation
 *
 * @link      https://knapen.dev
 * @copyright Copyright (c) 2020 Kjell Knapen
 */

namespace marbles\teamleader\services;

use marbles\teamleader\TeamleaderApi as TeamleaderPlugin;

use Craft;
use craft\base\Component;
use craft\commerce\elements\Order;


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
class TeamleaderApi extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     TeamleaderApi::$plugin->teamleaderapi->exampleService()
     *
     * @return mixed
     */
    public function createInvoice(Order $order)
    {
        Craft::info(
            'Create invoice triggerd',
            __METHOD__
        );
    }
}

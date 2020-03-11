# Teamleader plugin for Craft CMS 3.x

A connection between craft commerce and teamleader to have an easy flow of invoicing

## Requirements

This plugin requires Craft CMS 3.0.0 or later and Craft Commerce 2 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require marbles/craft-teamleader

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Teamleader.

## Teamleader Overview

-Insert text here-

## Configuring Teamleader

Before you start using the teamleader plugin, you need to add a custom integration to your teamleader and put the redirect uri as: http://example.com/admin/teamleader/connect/integration where http://example.com is your website.

Now you can copy your clientId and clientSecret and add it to your config.

The config file looks like this:
```PHP
/**
 * Teamleader config.php
 *
 * This file exists only as a template for the Teamleader settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'teamleader.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [

    // Teamleader integration clientId
    "clientId" => '',

    // Teamleader integration clientSecret
    "clientSecret" => '',

    // Base Url of the app, this is needed to make your redirect uri
    "baseUrl" => '',

    // Id of the department where the invoices need to be saved
    "departmentId" => '',

    // Id of the tax rate you need for your invoices
    "taxRateId" => '',
];

```

Once this is setup you can go to the teamleader > connect tab in the Craft controlepanel and connect your teamleader to your commerce site.


## Teamleader Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Marbles](https://marbles.be)

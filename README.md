<p align="center"><img src="./src/icon.svg" width="100" height="100" alt="M-Pesa for Craft Commerce icon"></p>

<h1 align="center">M-Pesa for Craft Commerce</h1>

This plugin provides a [M-Pesa](https://mpesa.com/) integration for [Craft Commerce](https://craftcms.com/commerce).

## Requirements

This plugin requires Craft 4.0 and Craft Commerce 4.0 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “M-Pesa for Craft Commerce”. Then click on the “Install” button in its modal window.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require mwanziamutua/commerce-mpesa

# tell Craft to install the plugin
./craft install/plugin commerce-mpesa
```

## Setup

To add a M-Pesa payment gateway, go to Commerce → Settings → Gateways, create a new gateway, and set the gateway type to “M-Pesa”.

> **Tip:** The passkey is the key provided to you by M-Pesa during registration. These settings can be set to environment variables. See [Environmental Configuration](https://docs.craftcms.com/v3/config/environments.html) in the Craft docs to learn more about that.
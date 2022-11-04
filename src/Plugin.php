<?php

namespace craft\commerce\mpesa;

use craft\commerce\mpesa\gateways\Gateway;
use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

/**
 * Plugin represents the Flutterwave integration plugin.
 *
 * @author Atec, Inc. <support@atec.ke>
 * @since  1.0
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        Event::on(
            Gateways::class,
            Gateways::EVENT_REGISTER_GATEWAY_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = Gateway::class;
            }
        );
    }
}

<?php
/**
<<<<<<< HEAD
 * @link https://dimetechgroup.com/
 * @copyright Copyright (c) Dimetech Group.
=======
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
>>>>>>> 2f4c11c8fd43049583b2d32c4a99021ff629fda3
 */

namespace craft\commerce\mpesa\migrations;

use Craft;
use craft\commerce\flutterwave\gateways\Gateway;
use craft\db\Migration;
use craft\db\Query;
use yii\db\Exception;

/**
 * Installation Migration
 *
<<<<<<< HEAD
 * @author Dimetech Group <support@dimetechgroup.com>
 * @since  1.0.0
=======
 * @author Atec, Inc. <support@atec.ke>
 * @since  1.0
>>>>>>> 2f4c11c8fd43049583b2d32c4a99021ff629fda3
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Convert any built-in Flutterwave gateways to ours
        $this->_convertGateways();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        return true;
    }

    /**
     * Converts any old school Flutterwave gateways to this one
     *
     * @return void
     * @throws Exception
     */
    private function _convertGateways(): void
    {
        $gateways = (new Query())
            ->select(['id'])
            ->where(['type' => 'craft\\commerce\\gateways\\Mpesa'])
            ->from(['{{%commerce_gateways}}'])
            ->all();

        $dbConnection = Craft::$app->getDb();

        foreach ($gateways as $gateway) {
            $values = [
                'type' => Gateway::class,
            ];

            $dbConnection->createCommand()
                ->update('{{%commerce_gateways}}', $values, ['id' => $gateway['id']])
                ->execute();
        }
    }
}

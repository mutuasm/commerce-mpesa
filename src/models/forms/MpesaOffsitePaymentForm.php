<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\commerce\mpesa\models\forms;

use craft\commerce\models\payments\BasePaymentForm;

class MpesaOffsitePaymentForm extends BasePaymentForm
{
    /**
     * @var string|null
     */
    public ?string $phone = null;

    public function rules(): array
    {
        if (empty($this->phone)) {
            return [
                [['phone'], 'required'],
            ];
        }

        return [];
    }

}

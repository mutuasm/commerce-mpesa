<?php
/**
<<<<<<< HEAD
 * @link https://dimetechgroup.com/
 * @copyright Copyright (c) Dimetech Group.
=======
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
>>>>>>> 2f4c11c8fd43049583b2d32c4a99021ff629fda3
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

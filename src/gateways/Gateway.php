<?php
/**
 * @link https://dimetechgroup.com/
 * @copyright Copyright (c) Dimetech Group.

 */

namespace craft\commerce\mpesa\gateways;

use Craft;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\CurrencyException;
use craft\commerce\errors\OrderStatusException;
use craft\commerce\errors\TransactionException;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\Transaction;
use craft\commerce\mpesa\models\forms\MpesaOffsitePaymentForm;
use craft\commerce\mpesa\models\RequestResponse;
use craft\commerce\omnipay\base\OffsiteGateway;
use craft\commerce\Plugin as Commerce;
use craft\commerce\records\Transaction as TransactionRecord;
use craft\errors\ElementNotFoundException;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\web\Response;
use craft\web\View;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Issuer;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Common\PaymentMethod;
use Omnipay\Mpesa\Gateway as OmnipayGateway;
use Omnipay\Mpesa\Message\MpesaPurchaseRequest;
use Omnipay\Mpesa\Message\Response as MpesaResponse;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * Gateway represents Mpesa gateway
 *

 * @author    Dimetech Group <support@dimetechgroup.com>

 * @since     1.0
 *
 * @property bool $apiKey
 * @property-read null|string $settingsHtml
 */
class Gateway extends OffsiteGateway
{
    /**
     * @var string|null
     */
    private ?string $_shortcode = null;

    /**
     * @var string|null
     */
    private ?string $_consumerKey = null;

    /**
     * @var string|null
     */
    private ?string $_passKey = null;

    /**
     * @var array|null
     */
    private ?string $_consumerSecret = null;

    /**
     * @var bool|string
     */
    private bool|string $_testMode = false;

    /**
     * @inheritdoc
     */
    public function getSettings(): array
    {
        $settings = parent::getSettings();
        $settings['shortcode'] = $this->getShortcode(false);
        $settings['consumerKey'] = $this->getConsumerKey(false);
        $settings['consumerSecret'] = $this->getConsumerSecret(false);
        $settings['passKey'] = $this->getPassKey(false);
        $settings['testMode'] = $this->getTestMode(false);

        return $settings;
    }

    /**
     * @param bool $parse
     * @return string|null
     * @since 1.0.0
     */
    public function getShortcode(bool $parse = true): ?string
    {
        return $parse ? App::parseEnv($this->_shortcode) : $this->_shortcode;
    }

    /**
     * @param string|null $shortcode
     * @return void
     * @since 1.0.0
     */
    public function setShortcode(?string $shortcode): void
    {
        $this->_shortcode = $shortcode;
    }

    /**
     * @param bool $parse
     * @return string|null
     * @since 1.0.0
     */
    public function getConsumerKey(bool $parse = true): ?string
    {
        return $parse ? App::parseEnv($this->_consumerKey) : $this->_consumerKey;
    }

    /**
     * @param string|null $consumerKey
     * @return void
     * @since 1.0.0
     */
    public function setConsumerKey(?string $consumerKey): void
    {
        $this->_consumerKey = $consumerKey;
    }

    /**
     * @param bool $parse
     * @return string|null
     * @since 1.0.0
     */
    public function getConsumerSecret(bool $parse = true): ?string
    {
        return $parse ? App::parseEnv($this->_consumerSecret) : $this->_consumerSecret;
    }

    /**
     * @param string|null $consumerSecret
     * @return void
     * @since 1.0.0
     */
    public function setConsumerSecret(?string $consumerSecret): void
    {
        $this->_consumerSecret = $consumerSecret;
    }

    /**
     * @param bool $parse
     * @return string|null
     * @since 1.0.0
     */
    public function getPassKey(bool $parse = true): ?string
    {
        return $parse ? App::parseEnv($this->_shortcode) : $this->_shortcode;
    }

    /**
     * @param string|null $passKey
     * @return void
     * @since 1.0.0
     */
    public function setPassKey(?string $passKey): void
    {
        $this->_passKey = $passKey;
    }

    /**
     * @param bool $parse
     * @return bool|string
     * @since 4.0.0
     */
    public function getTestMode(bool $parse = true): bool|string
    {
        $isTest = $parse ? App::parseBooleanEnv($this->_testMode) : $this->_testMode;
        return $isTest ? 'sandbox' : 'live';
    }

    /**
     * @param bool|string $testMode
     * @return void
     * @since 4.0.0
     */
    public function setTestMode(bool|string $testMode): void
    {
        $this->_testMode = $testMode;
    }

    /**
     * @inheritdoc
     */
    public function populateRequest(array &$request, BasePaymentForm $paymentForm = null): void
    {
        if ($paymentForm) {
            /** @var MpesaOffsitePaymentForm $paymentForm */
            if ($paymentForm->phone) {
                $request['phone_number'] = $paymentForm->phone;
                $request['account'] = $request['order']['id'];
                $request['callbackUrl'] = $request['returnUrl'];;
            }

        }
    }

    public function supportsPaymentSources(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function supportsRefund(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function supportsCompletePurchase(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function supportsWebhooks(): bool
    {
        return true;
    }



    /**
     * @inheritDoc
     */
    public function purchase(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
    {
        return parent::purchase($transaction, $form);
    }


    /**
     * @inheritdoc
     */
    public function processWebHook(): Response
    {
        $rawData = Craft::$app->getRequest()->getRawBody();
        $response = Craft::$app->getResponse();
        $response->format = Response::FORMAT_RAW;

        $data = Json::decodeIfJson($rawData);

        if ($data) {
            try {

            } catch (\Throwable $exception) {
                Craft::$app->getErrorHandler()->logException($exception);
            }
        } else {
            Craft::warning('Could not decode JSON payload.', 'mpesa');
        }

        $response->data = 'ok';

        return $response;
    }


    /**
     * @inheritdoc
     
*/
    public function getPaymentTypeOptions(): array
    {
        return [
            'purchase' => Craft::t('commerce', 'Purchase (Authorize and Capture Immediately)'),
        ];
    }


    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'Mpesa');
    }



    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('commerce-mpesa/gatewaySettings', ['gateway' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentFormModel(): BasePaymentForm
    {
        return new MpesaOffsitePaymentForm();
    }

    /**
     * @inheritdoc
     */
    public function getPaymentFormHtml(array $params): ?string
    {
        try {
            $defaults = [
                'gateway' => $this,
                'paymentForm' => $this->getPaymentFormModel(),
            ];
        } catch (\Throwable $exception) {
            // In case this is not allowed for the account
            return parent::getPaymentFormHtml($params);
        }


        $params = array_merge($defaults, $params);

        $view = Craft::$app->getView();

        $previousMode = $view->getTemplateMode();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = $view->renderTemplate('commerce-mpesa/paymentForm', $params);

        $view->setTemplateMode($previousMode);

        return $html;
    }


    /**
     * @inheritdoc
     */
    protected function createGateway(): AbstractGateway
    {
        /** @var OmnipayGateway $gateway */
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());

        $gateway->setShortCode($this->getShortcode());
        $gateway->setConsumerKey($this->getConsumerKey());
        $gateway->setConsumerSecret($this->getConsumerSecret());
        $gateway->setPassKey($this->getPassKey());
        $gateway->setTestMode($this->getTestMode());

        $commerceMpesa = Craft::$app->getPlugins()->getPluginInfo('commerce-mpesa');
        if ($commerceMpesa) {
            $gateway->addVersionString('MpesaCraftCommerce/' . $commerceMpesa['version']);
        }

        $commerce = Craft::$app->getPlugins()->getPluginInfo('commerce');
        if ($commerce) {
            $gateway->addVersionString('CraftCommerce/' . $commerce['version']);
        }
        $gateway->addVersionString('uap/MvVFR6uSW5NzK8Kq');

        return $gateway;
    }

    /**
     * @inheritdoc
     */
    protected function getGatewayClassName(): ?string
    {
        return '\\' . OmnipayGateway::class;
    }

    /**
     * @inheritdoc
     */
    protected function prepareResponse(ResponseInterface $response, Transaction $transaction): RequestResponseInterface
    {
        /** @var AbstractResponse $response */
        return new RequestResponse($response, $transaction);
    }
}

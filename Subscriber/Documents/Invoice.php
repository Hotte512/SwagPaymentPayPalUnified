<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SwagPaymentPayPalUnified\Subscriber\Documents;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use Shopware_Components_Snippet_Manager as SnippetManager;
use SwagPaymentPayPalUnified\Components\Document\InvoiceDocumentHandler;
use SwagPaymentPayPalUnified\Components\PaymentMethodProvider;
use SwagPaymentPayPalUnified\Components\Services\Plus\PaymentInstructionService;
use SwagPaymentPayPalUnified\PayPalBundle\PaymentType;

class Invoice implements SubscriberInterface
{
    /**
     * @var PaymentInstructionService
     */
    private $paymentInstructionsService;

    /**
     * @var Connection
     */
    private $dbalConnection;

    /**
     * @var SnippetManager
     */
    private $snippetManager;

    /**
     * @param PaymentInstructionService $paymentInstructionService
     * @param Connection                $dbalConnection
     * @param SnippetManager            $snippetManager
     */
    public function __construct(
        PaymentInstructionService $paymentInstructionService,
        Connection $dbalConnection,
        SnippetManager $snippetManager
    ) {
        $this->paymentInstructionsService = $paymentInstructionService;
        $this->dbalConnection = $dbalConnection;
        $this->snippetManager = $snippetManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_Document::assignValues::after' => 'onBeforeRenderDocument',
        ];
    }

    /**
     * @param \Enlight_Hook_HookArgs $args
     */
    public function onBeforeRenderDocument(\Enlight_Hook_HookArgs $args)
    {
        /** @var \Shopware_Components_Document $document */
        $document = $args->getSubject();

        if (!$document) {
            return;
        }

        $paymentMethodProvider = new PaymentMethodProvider();
        $unifiedPaymentId = $paymentMethodProvider->getPaymentId($this->dbalConnection);

        $orderPaymentMethodId = (int) $document->_order->payment['id'];

        //This order has not been payed with paypal unified.
        if ($orderPaymentMethodId !== $unifiedPaymentId) {
            return;
        }

        $paypalPaymentType = $document->_order->order->attributes['swag_paypal_unified_payment_type'];

        if ($paypalPaymentType !== PaymentType::PAYPAL_INVOICE) {
            return;
        }

        $orderNumber = $document->_order->order['ordernumber'];

        $documentHandler = new InvoiceDocumentHandler(
            $this->paymentInstructionsService,
            $this->dbalConnection,
            $this->snippetManager
        );
        $documentHandler->handleDocument($orderNumber, $document);
    }
}

<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\SyliusBluemediaPlugin\Action;

use AppBundle\AppBundle;
use AppBundle\Services\BlueMedia;
use Contelizer\SyliusBluemediaPlugin\Exception\PayUException;
use Contelizer\SyliusBluemediaPlugin\Bridge\OpenBluemediaBridgeInterface;
use Contelizer\SyliusBluemediaPlugin\SetBluemedia;
use Contelizer\SyliusBluemediaPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentRepository;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\Factory;
use Webmozart\Assert\Assert;
use Payum\Core\Payum;

/**
 * @author Damian Frańczuk <damian.franczuk@contelizer.pl>
 */
final class BluemediaAction implements ApiAwareInterface, ActionInterface
{
    private $api = [];

    /**
     * @var OpenBluemediaBridgeInterface
     */
    private $openBluemediaBridge;

    /**
     * @var Payum
     */
    private $payum;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * @param OpenBluemediaBridgeInterface $openPayUBridge
     * @param Payum $payum
     */
    public function __construct(OpenBluemediaBridgeInterface $openBluemediaBridge, Payum $payum, PaymentRepository $paymentRepository)
    {
        $this->payum = $payum;
        $this->openBluemediaBridge = $openBluemediaBridge;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        $this->contelizerExecute($request);
    }

    public function contelizerExecute($request){
        RequestNotSupportedException::assertSupports($this, $request);
        $environment = $this->api['environment'];
        $signature = $this->api['signature_key'];
        $posId = $this->api['pos_id'];
        $model = ArrayObject::ensureArrayObject($request->getModel());
        $id = $request->getToken()->getDetails()->getId();
        $bluemedia = new BlueMedia();

        $payment = $this->paymentRepository->find($id);
        if($payment->getOrder()->getSubscription()){
            $link = $bluemedia->getRecursivePaymentLink($posId,$signature,$model['totalAmount'],$id);
        }else{
            $link = $bluemedia->getPaymentLink($posId,$signature,$model['totalAmount'],$id);
        }

        throw new HttpRedirect($link);
    }


    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SetBluemedia &&
            $request->getModel() instanceof \ArrayObject
            ;
    }

    /**
     * @return OpenBluemediaBridgeInterface
     */
    public function getOpenBluemediaBridge()
    {
        return $this->openBluemediaBridge;
    }

    /**
     * @param OpenBluemediaBridgeInterface $openPayUBridge
     */
    public function setOpenPayUBridge($openBluemediaBridge)
    {
        $this->openBluemediaBridge = $openBluemediaBridge;
    }

    private function prepareOrder(TokenInterface $token, $model, $posId)
    {
        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $order = [];
        $order['continueUrl'] = $token->getTargetUrl();
        $order['notifyUrl'] = $notifyToken->getTargetUrl();
        $order['customerIp'] = $model['customerIp'];
        $order['merchantPosId'] = $posId;
        $order['description'] = $model['description'];
        $order['currencyCode'] = $model['currencyCode'];
        $order['totalAmount'] = $model['totalAmount'];
        $order['extOrderId'] = $model['extOrderId'];
        /** @var CustomerInterface $customer */
        $customer = $model['customer'];

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class
            )
        );

        $buyer = [
            'email' => (string) $customer->getEmail(),
            'firstName' => (string) $customer->getFirstName(),
            'lastName' => (string) $customer->getLastName(),
            'language' => $model['locale'],
        ];

        $order['buyer'] = $buyer;
        $order['products'] = $this->resolveProducts($model);

        return $order;
    }

    /**
     * @param $model
     *
     * @return array
     */
    private function resolveProducts($model)
    {
        if (!array_key_exists('products', $model) || count($model['products']) === 0) {
            return [
                [
                    'name' => $model['description'],
                    'unitPrice' => $model['totalAmount'],
                    'quantity' => 1
                ]
            ];
        }

        return $model['products'];
    }

    /**
     * @param string $gatewayName
     * @param object $model
     *
     * @return TokenInterface
     */
    private function createNotifyToken($gatewayName, $model)
    {
        return $this->payum->getTokenFactory()->createNotifyToken(
            $gatewayName,
            $model
        );
    }
}

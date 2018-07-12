<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\SyliusBluemediaPlugin\Action;

use Contelizer\SyliusBluemediaPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

/**
 * @author Damian Frańczuk <damian.franczuk@contelizer.pl>
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = $request->getModel();
        ArrayObject::ensureArrayObject($model);

        $order = $request->getFirstModel()->getOrder();
        $model['customer'] = $order->getCustomer();
        $model['locale'] = $this->getFallbackLocaleCode($order->getLocaleCode());

        $payUAction = $this->getPayUAction($request->getToken(), $model);

        $this->getGateway()->execute($payUAction);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param TokenInterface $token
     * @param ArrayObject $model
     *
     * @return SetPayU
     */
    private function getPayUAction(TokenInterface $token, ArrayObject $model)
    {
        $payUAction = new SetPayU($token);
        $payUAction->setModel($model);

        return $payUAction;
    }

    private function getFallbackLocaleCode($localeCode)
    {
        return explode('_', $localeCode)[0];
    }
}

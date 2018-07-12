<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\SyliusBluemediaPlugin\Action;

use Contelizer\SyliusBluemediaPlugin\Bridge\OpenBluemediaBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

/**
 * @author Damian Frańczuk <damian.franczuk@contelizer.pl>
 */
final class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = isset($model['statusPayU']) ? $model['statusPayU'] : null;

        if ((null === $status || OpenBluemediaBridgeInterface::NEW_API_STATUS === $status) && false === isset($model['orderId'])) {
            $request->markNew();
            return;
        }

        if (OpenBluemediaBridgeInterface::PENDING_API_STATUS === $status) {
            return;
        }

        if (OpenBluemediaBridgeInterface::CANCELED_API_STATUS === $status) {
            $request->markCanceled();
            return;
        }

        if (OpenBluemediaBridgeInterface::WAITING_FOR_CONFIRMATION_PAYMENT_STATUS === $status) {
            $request->markSuspended();
            return;
        }

        if (OpenBluemediaBridgeInterface::COMPLETED_API_STATUS === $status) {
            $request->markCaptured();
            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}

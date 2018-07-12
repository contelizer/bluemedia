<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\Bluemedia\Bridge;

/**
 * @author Damian Frańczuk <damian.franczuk@contelizer.pl>
 * @author Dawid Majka <dawid.majka@contelizer.pl>
 */
final class OpenBluemediaBridge implements OpenBluemediaBridgeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setAuthorizationDataApi($environment, $signatureKey, $posId)
    {
        \OpenBluemedia_Configuration::setEnvironment($environment);
        \OpenBluemedia_Configuration::setSignatureKey($signatureKey);
        \OpenBluemedia_Configuration::setMerchantPosId($posId);
    }

    /**
     * {@inheritDoc}
     */
    public function create($order)
    {
        return \OpenBluemedia_Order::create($order);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve($orderId)
    {
        return \OpenBluemedia_Order::retrieve($orderId);
    }

    /**
     * {@inheritDoc}
     */
    public function consumeNotification($data)
    {
        return \OpenBluemedia_Order::consumeNotification($data);
    }
}

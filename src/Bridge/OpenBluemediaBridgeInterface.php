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
 * @author Dawid Majka <dawid.majka@contelizer.pl>
 */
interface OpenBluemediaBridgeInterface
{
    const NEW_API_STATUS = 'NEW';
    const PENDING_API_STATUS = 'PENDING';
    const COMPLETED_API_STATUS = 'COMPLETED';
    const SUCCESS_API_STATUS = 'SUCCESS';
    const CANCELED_API_STATUS = 'CANCELED';
    const COMPLETED_PAYMENT_STATUS = 'COMPLETED';
    const PENDING_PAYMENT_STATUS = 'PENDING';
    const CANCELED_PAYMENT_STATUS = 'CANCELED';
    const WAITING_FOR_CONFIRMATION_PAYMENT_STATUS = 'WAITING_FOR_CONFIRMATION';
    const REJECTED_STATUS = 'REJECTED';

    /**
     * @param $environment
     * @param $signatureKey
     * @param $posId
     */
    public function setAuthorizationDataApi($environment, $signatureKey, $posId);

    /**
     * @param $order
     */
    public function create($order);

    /**
     * @param string $orderId
     *
     * @return object
     */
    public function retrieve($orderId);

    /**
     * @param $data
     * @return null|\OpenPayU_Result
     *
     * @throws \OpenPayU_Exception
     */
    public function consumeNotification($data);
}
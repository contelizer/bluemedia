<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Tests\Contelizer\SyliusBluemediaPlugin\Behat\Mocker;

use Contelizer\SyliusBluemediaPlugin\Bridge\OpenBluemediaBridge;
use Contelizer\SyliusBluemediaPlugin\Bridge\OpenBluemediaBridgeInterface;
use Sylius\Behat\Service\Mocker\Mocker;

/**
 * @author Dawid Majka <dawid.majka@contelizer.pl>
 */
final class PayUApiMocker
{
    /**
     * @var Mocker
     */
    private $mocker;

    /**
     * @param Mocker $mocker
     */
    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    /**
     * @param callable $action
     */
    public function mockApiSuccessfulPaymentResponse(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenBluemediaBridgeInterface::class);

        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function completedPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenBluemediaBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenBluemediaBridge::COMPLETED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function canceledPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenBluemediaBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenBluemediaBridge::CANCELED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param $statusPayment
     *
     * @return \OpenPayU_Result
     */
    private function getDataRetrieve($statusPayment)
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenBluemediaBridge::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'orders' => [
                (object)[
                    'status' => $statusPayment
                ]
            ]
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }

    /**
     * @return \OpenPayU_Result
     */
    private function createResponseSuccessfulApi()
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenBluemediaBridge::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'redirectUri' => '/'
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }
}
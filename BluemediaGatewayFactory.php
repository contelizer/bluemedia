<?php
namespace Contelizer\Bluemedia;

use Contelizer\Bluemedia\Action\AuthorizeAction;
use Contelizer\Bluemedia\Action\CancelAction;
use Contelizer\Bluemedia\Action\ConvertPaymentAction;
use Contelizer\Bluemedia\Action\CaptureAction;
use Contelizer\Bluemedia\Action\NotifyAction;
use Contelizer\Bluemedia\Action\RefundAction;
use Contelizer\Bluemedia\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class BluemediaGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'bluemedia',
            'payum.factory_title' => 'bluemedia',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}

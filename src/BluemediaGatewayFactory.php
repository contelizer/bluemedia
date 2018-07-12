<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\Bluemedia;

use Contelizer\Bluemedia\Action\CaptureAction;
use Contelizer\Bluemedia\Action\ConvertPaymentAction;
use Contelizer\Bluemedia\Action\NotifyAction;
use Contelizer\Bluemedia\Action\StatusAction;
use Contelizer\Bluemedia\Bridge\OpenBluemediaBridge;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class BluemediaGatewayFactory extends GatewayFactory
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
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => 'secure',
                'pos_id' => '',
                'signature_key' => ''
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['environment', 'pos_id', 'signature_key'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $payuConfig = [
                    'environment' => $config['environment'],
                    'pos_id' => $config['pos_id'],
                    'signature_key' => $config['signature_key'],
                ];

                return $payuConfig;
            };
        }
    }
}

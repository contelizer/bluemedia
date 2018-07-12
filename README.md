# Contelizer

The Payum extension to rapidly build new extensions.

1. Create new project

```bash
$ composer create-project contelizer/bluemedia
```

2. Register a gateway factory to the payum's builder and create a gateway:

```php
<?php

use Payum\Core\PayumBuilder;
use Payum\Core\GatewayFactoryInterface;

$defaultConfig = [];

$payum = (new PayumBuilder)
    ->addGatewayFactory('paypal', function(array $config, GatewayFactoryInterface $coreGatewayFactory) {
        return new \Acme\Paypal\PaypalGatewayFactory($config, $coreGatewayFactory);
    })

    ->addGateway('paypal', [
        'factory' => 'paypal',
        'sandbox' => true,
    ])

    ->getPayum()
;
```

3. While using the gateway implement all method where you get `Not implemented` exception:

```php
<?php

use Payum\Core\Request\Capture;

$paypal = $payum->getGateway('paypal');

$model = new \ArrayObject([
  // ...
]);

$paypal->execute(new Capture($model));
```

## Resources

* [Site](http://contelizer.pl/)

## Developed by Forma-Pro

Forma-Pro is a full stack development company which interests also spread to open source development. 
Being a team of strong professionals we have an aim an ability to help community by developing cutting edge solutions in the areas of e-commerce, docker & microservice oriented architecture where we have accumulated a huge many-years experience. 
Our main specialization is Symfony framework based solution, but we are always looking to the technologies that allow us to do our job the best way. We are committed to creating solutions that revolutionize the way how things are developed in aspects of architecture & scalability.

If you have any questions and inquires about our open source development, this product particularly or any other matter feel free to contact at opensource@forma-pro.com

## License

Contelizer Bluemedia plugin  is released under the [MIT License](LICENSE).

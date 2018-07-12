<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Tests\Contelizer\Bluemedia\Behat\Page\External;

use Behat\Mink\Session;
use Payum\Core\Security\TokenInterface;
use Sylius\Behat\Page\Page;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Dawid Majka <dawid.majka@contelizer.pl>
 */
final class PayUCheckoutPage extends Page implements PayUCheckoutPageInterface
{
    /**
     * @var RepositoryInterface
     */
    private $securityTokenRepository;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RepositoryInterface $securityTokenRepository
     */
    public function __construct(Session $session, array $parameters, RepositoryInterface $securityTokenRepository)
    {
        parent::__construct($session, $parameters);

        $this->securityTokenRepository = $securityTokenRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function pay()
    {
        $this->getDriver()->visit($this->findCaptureToken()->getTargetUrl());
    }

    /**
     * {@inheritDoc}
     */
    public function cancel()
    {
        $this->getDriver()->visit($this->findCaptureToken()->getTargetUrl());
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = [])
    {
        return 'https://secure.payu.com/api/v2_1/orders';
    }

    /**
     * @return TokenInterface
     *
     * @throws \RuntimeException
     */
    private function findCaptureToken()
    {
        $tokens = $this->securityTokenRepository->findAll();

        /** @var TokenInterface $token */
        foreach ($tokens as $token) {
            if (strpos($token->getTargetUrl(), 'capture')) {
                return $token;
            }
        }

        throw new \RuntimeException('Cannot find capture token, check if you are after proper checkout steps');
    }
}
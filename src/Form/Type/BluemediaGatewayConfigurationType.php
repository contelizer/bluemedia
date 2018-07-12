<?php

/**
 * This file was created by the developers from Contelizer.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://contelizer.pl and write us
 * an email on biuro@contelizer.pl.
 */

namespace Contelizer\Bluemedia\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Damian Frańczuk <damian.franczuk@contelizer.pl>
 */
final class BluemediaGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'contelizer.payu_plugin.secure' => 'secure',
                    'contelizer.payu_plugin.sandbox' => 'sandbox',
                ],
                'label' => 'contelizer.payu_plugin.environment',
            ])
            ->add('signature_key', TextType::class, [
                'label' => 'contelizer.payu_plugin.signature_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'contelizer.payu_plugin.gateway_configuration.signature_key.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('pos_id', TextType::class, [
                'label' => 'contelizer.payu_plugin.pos_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'contelizer.payu_plugin.gateway_configuration.pos_id.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
        ;
    }
}

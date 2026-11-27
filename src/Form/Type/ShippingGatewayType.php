<?php

declare(strict_types=1);

namespace Waaz\SyliusChronopostPlugin\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Core\Model\ShippingMethod;

final class ShippingGatewayType extends AbstractType
{
    static array $products = [
        'CHRONO10',
        'CHRONO13',
        'CHRONO18',
        'CHRONORELAIS',
        'CHRONOCLASSIC',
        'CHRONOEXPRESS',
        'RELAISEUROPE',
        'RELAISDOM',
        'SAMEDAY',
        'CHRONORDV',
        'CHRONOFRESH13',
        'CHRONOFREEZE13',
        'SHOPTOSHOP'
    ];

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contractNumber', TextType::class, [
                'label' => 'waaz.ui.chronopost.username',
                'required' => true
            ])
            ->add('subAccount', TextType::class, [
                'label' => 'waaz.ui.chronopost.sub_account',
                'required' => false
            ])
            ->add('password', TextType::class, [
                'label' => 'waaz.ui.chronopost.password',
                'required' => true,
            ])
            ->add('expeditorAddress', CompanyType::class, [
                'label' => 'waaz.ui.chronopost.shipping_address.label'
            ])
            ->add('billingAddress', CompanyType::class, [
                'label' => 'waaz.ui.chronopost.billing_address.label'
            ])
            ->add('returnAddress', CompanyType::class, [
                'label' => 'waaz.ui.chronopost.return_address.label'
            ])

            ->add('print_mode', ChoiceType::class, [
                'label' => 'waaz.ui.chronopost.print_mode',
                'required' => true,
                'choices' => [
                    'Fichier PDF' => 'PDF',
                    'Imprimante thermique' => 'THE',
                    'Format PDF sans preuve de dépôt' => 'SPD'
                ]
            ])
        ;

        foreach (self::$products as $product) {
            foreach ($this->em->getRepository(ShippingMethod::class)->findAll() as $shippingMethod) {
                $choices[$shippingMethod->getCode()] = $shippingMethod->getId();
            }

            $builder->add('product_'.$product, ChoiceType::class, [
                'label' => 'waaz.ui.chronopost.products.'.$product,
                'choices' => $choices,
                'multiple' => true,
                'required' => false,
            ]);
        }
    }
}

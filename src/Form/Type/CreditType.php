<?php


namespace Acme\SyliusExamplePlugin\Form\Type;


use Sylius\Component\Currency\Model\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CreditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', TextType::class, [
                'label' => 'eres_digital_wallet.admin.form.amount',
                'constraints' => [
                    new NotBlank(),
                    new Positive()
                ],
            ])
            ->add('currencyCode', EntityType::class, [
                'label' => 'eres_digital_wallet.admin.form.currency_code',
                'class' => Currency::class,
                'choice_label' => 'code',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('action', TextType::class, [
                'label' => 'eres_digital_wallet.admin.form.action',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }
}

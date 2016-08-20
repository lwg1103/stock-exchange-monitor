<?php

namespace AppBundle\Form;

use Report\Entity\Report\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReportType
 * 
 * @package AppBundle\Form
 */
class ReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface  $builder
     * @param array                 $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('identifier')
            ->add('period', ChoiceType::class, [
                'choices' => [
                    'annually' => Period::ANNUALLY,
                    'biannually' => Period::BIANNUAL,
                    'quarterly' => Period::QUARTERLY,
                ]
            ])
            ->add('income')
            ->add('netProfit')
            ->add('operationalNetProfit')
            ->add('bookValue')
            ->add('assets')
            ->add('currentAssets')
            ->add('liabilities')
            ->add('currentLiabilities')
            ->add('sharesQuantity')
            ->add('company')
            ->add('add', SubmitType::class, array('label' => 'Add'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Report\Entity\Report'
        ));
    }
}

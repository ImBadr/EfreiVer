<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                "choices" => array(
                    "contains plagiarized content" => "contains plagiarized content",
                    "contains offensive language" => "contains offensive language",
                    "contains pornographic content" => "contains pornographic content",
                    "scam" => "scam",
                    "service not performed" => "service not performed",
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}

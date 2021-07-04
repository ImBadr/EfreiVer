<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Offre;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, [
                "attr" => [
                    "class" => "form__input",
                    "placeholder" =>"Title",
                ]
            ])
            ->add('description', TextareaType::class, [
                "attr" => [
                    "class" => "form__textarea",
                    "placeholder" =>"Description ...",
                ]
            ])
            ->add('images', FileType::class, [
                "attr" => [
                    "class" => "form-control",
                    "accept" => "image/*"
                ],
                "label" => "Select images",
                "required" => false,
                "multiple" => true,
            ])
            ->add('price', NumberType::class, [
                "attr" => [
                    "class" => "form__input",
                    "placeholder" =>"Price",
                ]
            ])
            ->add('category', EntityType::class, [
                    "attr" => [
                        "class" => "filter__select"
                    ],
                    "class" => Category::class,
                    "choice_label" => "name",
                    "multiple" => false,
                    "required" => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}

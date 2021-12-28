<?php
/**
 * This is the form for trick image
 */
namespace App\Type;

use App\Entity\TrickImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builderInterface, array $option)
    {

        $builderInterface
        ->add('image', FileType::class, [
            "label" => false,
            "mapped" => false,
            "constraints" => [
                new File([
                    'maxSize' => '5024k',
                    'mimeTypes' => [
                        'image/*'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                ])
            ]
        ])
        ->add('save', SubmitType::class)
        ;

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickImage::class
        ]);
    }
}
<?php
/**
 * Avatar form 
 */
namespace App\Type;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("image", FileType::class, [
            "mapped" => false,
            "label" => false,
            "constraints" => [
                new File([
                    "maxSize" => "2048k",
                    "mimeTypes" => [
                        "image/*"
                    ],
                    "mimeTypesMessage" => "Please upload a valid image",
                ])
            ]
        ])
        ->add("submit", SubmitType::class, ["label" => "Update avatar"]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Avatar::class
        ]);
    }
}
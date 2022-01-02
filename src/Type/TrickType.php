<?php
/**
 * This is the form for trick
 */
namespace App\Type;

use App\Entity\Trick;
use App\Entity\TrickType as EntityTrickType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File as FileFile;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builderInterface, array $option)
    {

        if($option["createName"]) {

            $builderInterface
            ->add("nameTrick", TextType::class, [
                "help" => "Trick name"
            ]);

        }

        $builderInterface
            ->add("descriptionTrick", TextareaType::class, [
                "help" => "Trick description"
            ])
        ;

        $builderInterface->add('images', FileType::class, [
            "label" => false,
            "multiple" => true,
            "mapped" => false,
            "required" => false,
            "constraints" => [
                new All([
                    new File([
                        'maxSize' => '5024k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ])
            ]
        ]);

        $builderInterface->add('medias', CollectionType::class, [
            "label" => false,
            "entry_type" => UrlType::class,
            "prototype" => true,
            "allow_add" => true,
            "allow_delete" => true,
            "mapped" => false,
            "required" => false
        ]);

        $builderInterface->add("trickType", EntityType::class, [
            "class" => EntityTrickType::class,
            "choice_label" => "nameTrickType",
            "placeholder" => "Select trick type"
        ]);

        if($option["createType"]) {

            $builderInterface->add("addNewType", CheckboxType::class, [
                "mapped" => false,
                "required" => false
            ]);

            $funEvent = function (FormInterface $form, $data) {

                if($data === "1") {
                    $form->add("newTrickType", TextType::class, [
                        "mapped" => false
                    ]);
                }
    
            };
    
            $builderInterface->get("addNewType")->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($funEvent) {
                $data = $event->getData();
                $form = $event->getForm()->getParent();
    
                $funEvent($form, $data);
            });

        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Trick::class,
            "createName" => true,
            "createType" => true
        ]);
    }

}

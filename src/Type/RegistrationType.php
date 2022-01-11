<?php
/**
 * This is the registration form class
 */
namespace App\Type;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builderInterface, array $option)
    {
        $builderInterface
            ->add("name", TextType::class, [
                "help" => "This will be your username to connect",
                "label" => "Username"
            ])
            ->add("email", EmailType::class, [
                "help" => "A confirmation message will be send to this address"
            ])
            ->add("password", PasswordType::class, [
                "help" => "This will be your password to connect"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

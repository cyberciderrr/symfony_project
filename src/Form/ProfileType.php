<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Имя пользователя',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите имя пользователя',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Имя пользователя должно быть не менее {{ limit }} символов',
                        'maxMessage' => 'Имя пользователя должно быть не более {{ limit }} символов',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
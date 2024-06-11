<?php

namespace App\Form;

use App\Entity\Character;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('kind', TextType::class)
            ->add('surname', TextType::class)
            ->add('caste', TextType::class, array(
                'required' => false,
                'help' => 'Caste du Character',
            )
            )
            ->add('knowledge', TextType::class, array(
                'required' => false,
            )
            )
            ->add('intelligence', IntegerType::class, array(
                'required' => false,
                'help' => 'Niveau d\'intelligence du Character (1-250)',
                'attr' => array(
                    'min' => 1,
                    'max' => 250,
                ),
            )
            )
            ->add('strength', IntegerType::class, array(
                'required' => false,
                'label' => 'Niveau de force',
                'attr' => array(
                    'min' => 1,
                    'max' => 250,
                    'placeholder' => 'Niveau de force du Character (1-250)',
                ),
            )
            )
            ->add('image', TextType::class, array(
                'required' => false,
            )
            )
            ->add('creation', null, [
                'widget' => 'single_text',
            ])
            ->add('modification', null, [
                'widget' => 'single_text',
            ])
            ->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}

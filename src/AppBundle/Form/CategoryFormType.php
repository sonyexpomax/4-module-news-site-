<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\SubFamily;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\SubFamilyRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Название',
            ))
            ->add('alias')
            ->add('parentId', EntityType::class, [
                'label'    => 'Родительская категория (если есть)',
                'class' => Category::class,
                'choice_label' => 'name',
                    'placeholder' => 'Выберите категорию',
                'query_builder' => function(CategoryRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                },
                    'empty_data' => 'friend',

                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Category'
        ]);
    }


}
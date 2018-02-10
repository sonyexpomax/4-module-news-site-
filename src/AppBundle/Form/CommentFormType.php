<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\CommentRepository;
use AppBundle\Repository\NewsRepository;
use AppBundle\Repository\TagRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, array(
                'attr' => array('cols' => '5', 'rows' => '11'),
                'label' => 'Содержание',
                ))
            ->add('createdAt', DateTimeType::class, array(
                'label' => 'Дата создания',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd'
            ))
            ->add('isActive', CheckboxType::class, array(
                'label'    => 'Активен ли этот комментарий',
                'required' => false
            ))
            ->add('user', EntityType::class, [
                'label'    => 'Пользователь',
                'class' => User::class,
                'choice_label' => 'name',
                'query_builder' => function(UserRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('parentId', EntityType::class, [
                'label'    => 'Комментируемый отзыв (если есть)',
                'class' => Comment::class,
                'choice_label' => 'text',
                'query_builder' => function(CommentRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])

            ->add('save', SubmitType::class)
        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Comment'
        ]);
    }
}
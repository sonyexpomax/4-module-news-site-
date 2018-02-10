<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Images;
use AppBundle\Entity\Tag;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\TagRepository;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Название'
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array('cols' => '5', 'rows' => '11'),
                'label' => 'Содержание',
                ))
            ->add('createdAt', DateTimeType::class, array(
                'label' => 'Дата создания',
                'data' => new \DateTime(),
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd'
            ))
            ->add('isAnalytic', CheckboxType::class, array(
                'label'    => 'Аналитическая ли это статья?',
                'required' => false
            ))
            ->add('category', EntityType::class, [
                'placeholder' => 'Выбор категории',
                'label'    => 'Выбор категории',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('images', FileType::class, array(
                'label' => 'Загружаемые картинки (фото)',
                "data_class" => null,
                'multiple' => true,
            ))
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\News'
        ]);
    }






}
<?php

namespace App\Form;

use App\Entity\Categorias;
use App\Entity\Productos;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('peso')
            ->add('stock')
            ->add('categoria', EntityType::class, [
                'class' => Categorias::class,
                'choice_label' => 'nombre', // Muestra el nombre de la categoría en lugar del ID
                'choice_value' => 'id',     // Envía el ID al controlador
            ])
            ->add('precio')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Productos::class,
        ]);
    }
}

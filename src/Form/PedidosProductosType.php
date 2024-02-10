<?php

namespace App\Form;

use App\Entity\pedidos;
use App\Entity\PedidosProductos;
use App\Entity\productos;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedidosProductosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unidades')
            ->add('pedido', EntityType::class, [
                'class' => pedidos::class,
'choice_label' => 'id',
            ])
            ->add('producto', EntityType::class, [
                'class' => productos::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PedidosProductos::class,
        ]);
    }
}

<?php

namespace App\Form\Vendas;

use App\Entity\Vendas\PVItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PVItemType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var PVItem $pvItem */
            $pvItem = $event->getData();
            $builder = $event->getForm();

            $builder->add('id', HiddenType::class, array(
                'label' => 'Código',
                'required' => false
            ));

            $builder->add('produtoCodDesc', TextType::class, array(
                'label' => 'Produto',
                'required' => false,
                'mapped' => false,
                'disabled' => true,
                'data' => $pvItem->getProdutoCod() . ' - ' . $pvItem->getProdutoDesc()
            ));

            $builder->add('fornecedorCodDesc', TextType::class, array(
                'label' => 'Fornecedor',
                'required' => false,
                'mapped' => false,
                'disabled' => true,
                'data' => $pvItem->getCodFornecedor() . ' - ' . $pvItem->getNomeFornecedor()
            ));

            $builder->add('produtoCod', HiddenType::class, array(
                'required' => false
            ));

            $builder->add('produtoDesc', HiddenType::class, array(
                'required' => false
            ));

            $builder->add('codFornecedor', HiddenType::class, array(
                'required' => false
            ));

            $builder->add('nomeFornecedor', HiddenType::class, array(
                'required' => false
            ));

            $builder->add('precoCusto', HiddenType::class, array(
                'required' => false,
            ));

            $builder->add('precoVenda', MoneyType::class, [
                'label' => 'Preço Venda',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => true,
                'attr' => [
                    'class' => 'crsr-money',
                    'readonly' => true
                ]
            ]);

            $builder->add('qtde', NumberType::class, [
                'label' => 'Qtde',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => [
                    'class' => 'crsr-dec3'
                ],
                'required' => true
            ]);

            $builder->add('precoOrc', MoneyType::class, [
                'label' => 'Valor Unit',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => true,
                'attr' => [
                    'class' => 'crsr-money'
                ]
            ]);

            $builder->add('desconto', MoneyType::class, [
                'label' => 'Valor Desconto',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => true,
                'attr' => [
                    'class' => 'crsr-money'
                ]
            ]);

            $builder->add('total', MoneyType::class, [
                'label' => 'Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => [
                    'class' => 'crsr-money'
                ],
            ]);

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PVItem::class
        ));
    }
}
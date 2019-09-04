<?php

namespace App\Form\Relatorios;

use App\Entity\Relatorios\RelCliente01;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCliente01Type extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('codigo', IntegerType::class, [
            'label' => 'Código',
            'required' => false
        ]);

        $builder->add('nome', TextType::class, [
            'label' => 'Nome',
            'attr' => [
                'class' => 'focusOnReady'
            ],
        ]);

        $builder->add('documento', TextType::class, [
            'label' => 'CPF/CNPJ',
            'attr' => [
                'class' => 'cpfCnpj'
            ],
        ]);

        $builder->add('rg', TextType::class, [
            'label' => 'RG',
        ]);

        $builder->add('cep', TextType::class, [
            'label' => 'CEP',
            'attr' => [
                'class' => 'cepComBtnConsulta',
                'data-campo-logradouro' => 'rel_cliente01_endereco',
                'data-prefixodoscampos' => 'rel_cliente01_'
            ],
            'required' => false
        ]);

        $builder->add('endereco', TextType::class, [
            'label' => 'Logradouro',
            'required' => false
        ]);

        $builder->add('bairro', TextType::class, [
            'label' => 'Bairro',
            'required' => false
        ]);

        $builder->add('cidade', TextType::class, [
            'label' => 'Cidade',
            'required' => false
        ]);

        $builder->add('estado', ChoiceType::class, [
            'label' => 'Estado',
            'choices' => [
                'Acre' => 'AC',
                'Alagoas' => 'AL',
                'Amapá' => 'AP',
                'Amazonas' => 'AM',
                'Bahia' => 'BA',
                'Ceará' => 'CE',
                'Distrito Federal' => 'DF',
                'Espírito Santo' => 'ES',
                'Goiás' => 'GO',
                'Maranhão' => 'MA',
                'Mato Grosso' => 'MT',
                'Mato Grosso do Sul' => 'MS',
                'Minas Gerais' => 'MG',
                'Pará' => 'PA',
                'Paraíba' => 'PB',
                'Paraná' => 'PR',
                'Pernambuco' => 'PE',
                'Piauí' => 'PI',
                'Rio de Janeiro' => 'RJ',
                'Rio Grande do Norte' => 'RN',
                'Rio Grande do Sul' => 'RS',
                'Rondônia' => 'RO',
                'Roraima' => 'RR',
                'Santa Catarina' => 'SC',
                'São Paulo' => 'SP',
                'Sergipe' => 'SE',
                'Tocantins' => 'TO'
            ],
            'required' => false,
            'attr' => ['class' => 'autoSelect2']
        ]);


        $builder->add('localizador', TextType::class, [
            'label' => 'Localizador',
            'required' => false
        ]);

        $builder->add('condPagto', TextType::class, [
            'label' => 'Cond Pagto',
            'required' => false
        ]);

        $builder->add('desbloqueioTmp', ChoiceType::class, [
            'label' => 'Desbloqueio Tmp',
            'choices' => [
                'Sim' => 'S',
                'Não' => 'N'
            ]
        ]);

        $builder->add('acCompras', MoneyType::class, [
            'label' => 'Ac Compras',
            'currency' => 'BRL',
            'grouping' => 'true',
            'attr' => [
                'class' => 'crsr-money'
            ],
            'required' => false
        ]);

        $builder->add('limiteCompras', MoneyType::class, [
            'label' => 'Limite Compras',
            'currency' => 'BRL',
            'grouping' => 'true',
            'attr' => [
                'class' => 'crsr-money'
            ],
            'required' => false
        ]);

        $builder->add('flagLibPreco', ChoiceType::class, [
            'label' => 'Lib Preço',
            'choices' => [
                'Sim' => true,
                'Não' => false
            ]
        ]);

        $builder->add('sugereConsulta', ChoiceType::class, [
            'label' => 'Sugere Consulta',
            'choices' => [
                'Sim' => 'S',
                'Não' => 'N'
            ]
        ]);

        $builder->add('clienteBloqueado', ChoiceType::class, [
            'label' => 'Cliente Bloqueado',
            'choices' => [
                'Sim' => 'S',
                'Não' => 'N'
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RelCliente01::class
        ));
    }
}
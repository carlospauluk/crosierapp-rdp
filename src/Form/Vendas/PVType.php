<?php

namespace App\Form\Vendas;

use App\Entity\Vendas\PV;
use App\Repository\Vendas\PVRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PVType extends AbstractType
{

    /** @var RegistryInterface */
    private $doctrine;

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('uuid', TextType::class, [
            'label' => 'UUID',
            'attr' => [
                'readonly' => true,
            ],
            'required' => false,
        ]);

        $builder->add('pvEkt', IntegerType::class, [
            'label' => 'PV (EKT)',
            'attr' => [
                'readonly' => true,
            ],
            'required' => false,
        ]);

        $builder->add('status', TextType::class, [
            'label' => 'Status',
            'attr' => [
                'readonly' => true,
            ],
            'required' => false,
        ]);

        $builder->add('dtEmissao', DateTimeType::class, [
            'label' => 'Dt Emissão',
            'widget' => 'single_text',
            'required' => true,
            'format' => 'dd/MM/yyyy HH:ii:ss',
            'attr' => [
                'class' => 'crsr-datetime focusOnReady'
            ]
        ]);

        /** @var PVRepository $repoPV */
        $repoPV = $this->doctrine->getRepository(PV::class);
        $filialChoices = $repoPV->getFiliais();

        $builder->add('filial', ChoiceType::class, [
            'label' => 'Filial',
            'choices' => $filialChoices
        ]);

        $builder->add('vendedorCod', IntegerType::class, [
            'label' => 'Código'
        ]);

        $builder->add('vendedorNome', TextType::class, [
            'label' => 'Nome',
        ]);


        $builder->add('clienteCod', IntegerType::class, [
            'label' => 'Código'
        ]);

        $builder->add('clienteDocumento', TextType::class, [
            'label' => 'CPF/CNPJ',
            'attr' => [
                'class' => 'cpfCnpj'
            ],
        ]);

        $builder->add('clienteNome', TextType::class, [
            'label' => 'Nome',
        ]);

        $builder->add('obs', TextareaType::class, [
            'label' => 'Obs',
        ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PV::class
        ));
    }
}
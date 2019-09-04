<?php

namespace App\Form\Vendas;

use App\Entity\Vendas\PV;
use App\Repository\Vendas\PVRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var PV $pv */
            $pv = $event->getData();
            $builder = $event->getForm();

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

            $builder->add('vendedor', TextType::class, [
                'label' => 'Vendedor',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
            ]);

            $builder->add('dtEmissao', DateTimeType::class, [
                'label' => 'Dt Emissão',
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd/MM/yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'crsr-datetime focusOnReady'
                ]
            ]);

            /** @var PVRepository $repoPV */
            $repoPV = $this->doctrine->getRepository(PV::class);
            $filialChoices = $repoPV->getFiliais();

            $builder->add('filial', ChoiceType::class, [
                'label' => 'Filial',
                'choices' => $filialChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ]
            ]);

            $builder->add('status', TextType::class, [
                'label' => 'Status',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
            ]);


            $clienteChoices = null;
            $clienteData = null;
            if ($pv->getCliente()) {
                $clienteChoices[$pv->getCliente()] = urlencode($pv->getCliente());
                $clienteData = urlencode($pv->getCliente());
            }
            $builder->add('pessoa', ChoiceType::class, [
                'label' => 'Cliente',
                'choices' => $clienteChoices,
                'data' => $clienteData,
                'attr' => [
                    'class' => 'autoSelect2',
                    'data-route-url' => '/ven/pv/findClienteByStr/',
                ]
            ]);


            $builder->add('clienteCod', HiddenType::class);
            $builder->add('clienteDocumento', HiddenType::class);
            $builder->add('clienteNome', HiddenType::class);

            $builder->add('obs', TextareaType::class, [
                'label' => 'Obs',
            ]);


        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PV::class
        ));
    }
}
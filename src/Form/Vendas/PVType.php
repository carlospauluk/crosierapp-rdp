<?php

namespace App\Form\Vendas;

use App\Entity\Relatorios\RelCliente01;
use App\Entity\Vendas\PV;
use App\Repository\Relatorios\RelCliente01Repository;
use App\Repository\Vendas\PVRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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

    /** @var EntityManagerInterface */
    private $doctrine;

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var PV $pv */
            $pv = $event->getData();
            $builder = $event->getForm();

            /** @var PVRepository $repoPV */
            $repoPV = $this->doctrine->getRepository(PV::class);

            $disabled = $pv->getId() && $pv->getStatus() !== 'ABERTO';

            $builder->add('id', TextType::class, [
                'label' => 'ID',
                'disabled' => true,
            ]);

            $builder->add('uuid', TextType::class, [
                'label' => 'UUID',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
                'disabled' => $disabled,
            ]);

            $builder->add('pvEkt', IntegerType::class, [
                'label' => 'PV (EKT)',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
                'disabled' => $disabled,
            ]);

            $builder->add('status', TextType::class, [
                'label' => 'Status',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
                'disabled' => $disabled,
            ]);

            $vendedorChoices = $repoPV->getVendedores();

            $builder->add('vendedor', ChoiceType::class, [
                'label' => 'Vendedor',
                'choices' => $vendedorChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ],
                'disabled' => $disabled,
            ]);

            $builder->add('dtEmissao', DateTimeType::class, [
                'label' => 'Dt Emissão',
                'widget' => 'single_text',
                'required' => true,
                'html5' => false,
                'format' => 'dd/MM/yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'crsr-datetime focusOnReady'
                ],
                'disabled' => $disabled,
            ]);


            $filialChoices = $repoPV->getFiliais();

            $builder->add('filial', ChoiceType::class, [
                'label' => 'Filial',
                'choices' => $filialChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ],
                'disabled' => $disabled,
            ]);

            $builder->add('status', TextType::class, [
                'label' => 'Status',
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
                'disabled' => $disabled,
            ]);


            $clienteChoices = [null];
            $clienteVal = null;
            if ($pv->getCliente()) {
                $clienteChoices = [$pv->getCliente()];
                $clienteVal = $pv->getCliente();
            }
            $builder->add('cliente', EntityType::class, [
                'label' => 'Cliente',
                'class' => RelCliente01::class,
                'choices' => $clienteChoices,
                'data' => $clienteVal ?? null,
                'choice_label' => function (?RelCliente01 $cliente) {
                    return $cliente ? $cliente->getNomeMontado() : null;
                },
                'attr' => [
                    'class' => 'autoSelect2',
                    'data-val' => $clienteVal ? $clienteVal->getId() : '',
                    'data-route-url' => '/ven/pv/findClienteByStr/',
                ],
                'disabled' => $disabled,
            ]);


            $builder->add('clienteCod', HiddenType::class);
            $builder->add('clienteDocumento', HiddenType::class);
            $builder->add('clienteNome', HiddenType::class);


            $depositoChoices = [
                '0 - MATRIZ' => urlencode('0 - MATRIZ'),
                '1 - DELPOZO PG' => urlencode('1 - DELPOZO PG'),
                '2 - DELPOZO JUNDIAÍ' => urlencode('2 - DELPOZO JUNDIAI'),
                '10 - DEPÓSITO PG' => urlencode('10 - DEPOSITO PG'),
                '30 - TELÊMACO' => urlencode('30 - TELEMACO'),
                '31 - ACESSÓRIOS' => urlencode('31 - ACESSORIOS')
            ];

            $builder->add('deposito', ChoiceType::class, [
                'label' => 'Depósito',
                'choices' => $depositoChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ],
                'disabled' => $disabled,
            ]);


            $localizadorChoices = [
                '1 - BANCO' => urlencode('1 - BANCO'),
                '2 - CARTEIRA' => urlencode('2 - CARTEIRA'),
                '80 - BB PG' => urlencode('80 - BB PG'),
                '90 - ITAÚ PG' => urlencode('90 - ITAU PG'),
                '91 - ITAÚ TELÊMACO' => urlencode('91 - ITAU TELEMACO'),
                '92 - ITAÚ ACESSÓRIOS' => urlencode('91 - ITAU ACESSORIOS'),
            ];

            $builder->add('localizador', ChoiceType::class, [
                'label' => 'Localizador',
                'choices' => $localizadorChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ],
                'disabled' => $disabled,
            ]);


            $condPagtoChoices = [
                '1 - A VISTA' => urlencode('1 - A VISTA'),
                '2 - A PRAZO' => urlencode('2 - A PRAZO'),
                '50 - CHEQUE' => urlencode('50 - CHEQUE'),
                '100 - CARTÃO' => urlencode('100 - CARTAO'),
            ];

            $builder->add('condPagto', ChoiceType::class, [
                'label' => 'Cond Pagto',
                'choices' => $condPagtoChoices,
                'attr' => [
                    'class' => 'autoSelect2'
                ],
                'disabled' => $disabled,
            ]);


            $builder->add('obs', TextareaType::class, [
                'label' => 'Obs',
                'required' => false,
                'disabled' => $disabled,
            ]);

            $builder->add('subtotal', MoneyType::class, [
                'label' => 'Subtotal',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => [
                    'class' => 'crsr-money'
                ],
                'required' => false,
                'disabled' => true
            ]);

            $builder->add('descontos', MoneyType::class, [
                'label' => 'Descontos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => [
                    'class' => 'crsr-money'
                ],
                'required' => false,
                'disabled' => $disabled,
            ]);


            $builder->add('total', MoneyType::class, [
                'label' => 'Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => [
                    'class' => 'crsr-money'
                ],
                'required' => false,
                'disabled' => true
            ]);

            $venctos = $pv && $pv->getVenctos() ? json_decode($pv->getVenctos(), true) : null;

            for ($i = 1; $i <= 6; $i++) {

                $dtVencto = DateTimeUtils::parseDateStr($venctos[$i-1]['dtVencto'] ?? null);
                $valor = DecimalUtils::parseStr($venctos[$i-1]['valor'] ?? null);

                $builder->add('venctos_dt0' . $i, DateType::class, [
                    'widget' => 'single_text',
                    'mapped' => false,
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'data' => $dtVencto,
                    'attr' => [
                        'class' => 'crsr-date'
                    ],
                    'required' => false,
                    'disabled' => $disabled,
                ]);

                $builder->add('venctos_valor0' . $i, MoneyType::class, [
                    'currency' => 'BRL',
                    'grouping' => 'true',
                    'mapped' => false,
                    'data' => $valor,
                    'attr' => [
                        'class' => 'crsr-money'
                    ],
                    'required' => false,
                    'disabled' => $disabled,
                ]);
            }


        });


        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $clienteId = $event->getData()['cliente'] ?: null;
                /** @var RelCliente01Repository $repoRelCliente01 */
                $repoRelCliente01 = $this->doctrine->getRepository(RelCliente01::class);
                $cliente = $repoRelCliente01->find($clienteId);
                $clienteChoices = [$cliente];
                $form->remove('cliente');
                $form->add('cliente', EntityType::class, [
                    'label' => 'Cliente',
                    'class' => RelCliente01::class,
                    'choices' => $clienteChoices,
                    'data' => $cliente ?? null,
                    'choice_label' => function (?RelCliente01 $cliente) {
                        return $cliente ? $cliente->getNomeMontado() : null;
                    },
                    'attr' => [
                        'class' => 'autoSelect2',
                        'data-val' => $cliente ? $cliente->getId() : '',
                        'data-route-url' => '/ven/pv/findClienteByStr/',
                    ]
                ]);
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PV::class
        ));
    }
}
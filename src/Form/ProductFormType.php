<?php


namespace App\Form;

use App\Entity\Sweatshirt;
use App\Entity\ProductSize;
use App\Entity\Size;
use App\Form\ProductSizeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $em = $options['em'] ?? null;
        if (!$em) return;

        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('price', MoneyType::class, ['label' => 'Prix', 'currency' => false,'scale' => 2,'attr' => ['step' => '0.01'],'divisor' => 100,])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false,
            ])
            ->add('selection', CheckboxType::class, [
                'label' => 'Sélection du moment',
                'required' => false,
            ])
            ->add('productSizes', CollectionType::class, [
                'entry_type' => ProductSizeFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Tailles disponibles',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($em) {
            $sweatshirt = $event->getData();
            $form = $event->getForm();

            if (!$sweatshirt) return;

            $form->add('edit_id', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $sweatshirt->getId() ?? null,
            ]);

            if ($sweatshirt->getId()) return;

            $existingSizes = array_map(fn($ps) => $ps->getSize()?->getId(), $sweatshirt->getProductSizes()->toArray());

            $allSizes = $em->getRepository(Size::class)->findAll();
            foreach ($allSizes as $size) {
                if (!in_array($size->getId(), $existingSizes, true)) {
                    $productSize = new ProductSize();
                    $productSize->setSize($size);
                    $productSize->setProduct($sweatshirt);
                    $sweatshirt->addProductSize($productSize);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sweatshirt::class,
            'em' => null,
        ]);
    }
}
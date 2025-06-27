<?php

namespace App\DataFixtures;

use App\Entity\Sweatshirt;
use App\Entity\Size;
use App\Entity\ProductSize;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\SizeFixtures;

class SweatshirtFixtures extends Fixture implements DependentFixtureInterface
{   

    private function createSweat(ObjectManager $manager, array $sizes, string $name, int $price, bool $selection, string $imageName)
    {
        $sweat = new Sweatshirt();
        $sweat->setName($name);
        $sweat->setPrice($price);
        $sweat->setSelection($selection);
        $sweat->setImageName($imageName);
        $manager->persist($sweat);

        foreach ($sizes as $s) {
            $ps = new ProductSize();
            $ps->setProduct($sweat);
            $ps->setSize($s);
            $ps->setQuantity(2);
            $manager->persist($ps);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $sizeRepo = $manager->getRepository(Size::class);
        $sizes = [];
        foreach (['XS', 'S', 'M', 'L', 'XL'] as $label) {
            $size = $sizeRepo->findOneBy(['name' => $label]);
            if ($size) {
                $sizes[$label] = $size;
            }
        }

        $this->createSweat($manager, $sizes, 'Blackbelt', 2990, true, '6853b97b8ec58.jpg');
        $this->createSweat($manager, $sizes, 'Bluebelt', 2990, false, '6853b9c4842fd.jpg');
        $this->createSweat($manager, $sizes, 'Street', 3450, false, '6853b9ee9a50f.jpg');
        $this->createSweat($manager, $sizes, 'Pokeball', 4500, true, '6853ba06138b1.jpg');
        $this->createSweat($manager, $sizes, 'PinkLady', 2990, false, '6854be038f34d.jpg');
        $this->createSweat($manager, $sizes, 'Snow', 3200, false, '6853ba500e452.jpg');
        $this->createSweat($manager, $sizes, 'Greyback', 2850, false, '6853ba718f510.jpg');
        $this->createSweat($manager, $sizes, 'BlueCloud', 4500, false, '6853ba8de5c15.jpg');
        $this->createSweat($manager, $sizes, 'BornInUsa', 5990, true, '6853baa462bda.jpg');
        $this->createSweat($manager, $sizes, 'GreenSchool', 4220, false, '6853bab8efb8b.jpg');

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            SizeFixtures::class,
        ];
    }
}
<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $articles = 
        [
            [
                'cat'=>'Ordinateurs',
                'articles'=>
                [
                    [
                        'title' => 'Mini pc Asus','price' => '60000',
                        'buy' => '50000',
                        'etat'=>'Tendance',
                        'brand'=>'Asus'
                    ],
                    [
                        'title' => 'Mini pc Hp','price' => '60000',
                        'buy' => '50000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Dell E440',
                        'price' => '150000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Dell'
                    ],
                    [
                        'title' => 'Dell Lattitude','price' => '240000',
                        'buy' => '150000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Hp 215 G1','price' => '200000',
                        'buy' => '150000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Hp Elitebook 840 G3',
                        'price' => '220000',
                        'buy' => '200000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Lenovo x131e',
                        'price' => '85000',
                        'buy' => '70000',
                        'etat'=>'Tendance',
                        'brand'=>'Lenovo'
                    ],
                    [
                        'title' => 'Toshiba portege Z30',
                        'price' => '180000',
                        'buy' => '200000',
                        'etat'=>'Tendance',
                        'brand'=>'Lenovo'
                    ],
                    [
                        'title' => 'Toshiba portege',
                        'price' => '125000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Lenovo'
                    ],
                ]
            ],
            [
                'cat'=>'Clé usb',
                'articles'=>
                [
                    [
                        'title' => 'Clé Usb 32 go','price' => '10000',
                        'buy' => '6000',
                        'etat'=>'Top',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Clé Usb 8 go','price' => '4000',
                        'buy' => '3000',
                        'etat'=>'Top','brand'=>'Hp'
                    ],
                    [
                        'title' => 'Clé Usb 16 go','price' => '6500',
                        'buy' => '5000',
                        'etat'=>'Top','brand'=>'Hp'
                    ],
                    [
                        'title' => 'Clé Usb 2 go','price' => '2000',
                        'buy' => '3000',
                        'etat'=>'Top','brand'=>'Hp'
                    ]
                ]
            ],
            [
                'cat'=>'Cable Hdmi',
                'articles'=>
                [
                    [
                        'title' => 'Hdmi 1.5m','price' => '1000',
                        'buy' => '250000',
                        'etat'=>'Top'
                    ],
                    [
                        'title' => 'Hdmi 2m','price' => '2500',
                        'buy' => '2000',
                        'etat'=>'Top'
                    ],
                    [
                        'title' => 'Hdmi 6m','price' => '10000',
                        'buy' => '8000',
                        'etat'=>'Top'
                    ],
                ]
            ],
        ];

        
            foreach ($articles as $value) {
                $category  = $this->getReference(('category_'.str_replace(' ','_',$value['cat'])));
                foreach ($value['articles'] as $key => $value) {
                $article  = new Article();
                $article->setTitle($value['title'])
                ->setCategory($category)
                ->setBuyingPrice($value['buy'])
                ->setPrice($value['price'])
                ->setEnabled(true)
                ->setEtat($value['etat'])
                ->setDescription('Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae consequatur dicta,')
                ->setQuantity(10)
                ->setQtyReel(10);
                $this->addReference('_article_'. str_replace(' ','_', $value['title']),$article);
                $manager->persist($article);
                }
            }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
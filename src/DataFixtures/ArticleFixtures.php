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
                        'title' => 'Mini pc','price' => '60000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Mini pc 2','price' => '60000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Mini pc 3','price' => '60000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Hp probook','price' => '150000',
                        'buy' => '120000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Dell Lattitude 1','price' => '240000',
                        'buy' => '150000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Dell Lattitude 2','price' => '110000',
                        'buy' => '150000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Dell Lattitude 3','price' => '200000',
                        'buy' => '150000',
                        'etat'=>'Tendance',
                        'brand'=>'Hp'
                    ],
                    [
                        'title' => 'Mac probook','price' => '10000',
                        'buy' => '200000','etat'=>'Tendance','brand'=>'Hp'
                    ],
                    [
                        'title' => 'Mac probook 2','price' => '200000',
                        'buy' => '200000','etat'=>'Tendance','brand'=>'Hp'
                    ],
                    [
                        'title' => 'Mac probook 3','price' => '350000',
                        'buy' => '200000','etat'=>'Tendance','brand'=>'Hp'
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
                        'title' => 'Clé Usb 16 go','price' => '8000',
                        'buy' => '3000',
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
            // [
            //     'cat'=>'Iphone',
            //     'articles'=>
            //     [
            //         [
            //             'title' => 'Iphone 13','price' => '900000',
            //             'buy' => '700000',
            //             'etat'=>'Top'
            //         ],
            //         [
            //             'title' => 'Iphone 12','price' => '760000',
            //             'buy' => '700000',
            //             'etat'=>'Top'
            //         ],
            //         [
            //             'title' => 'Iphone 11','price' => '800000',
            //             'buy' => '700000',
            //             'etat'=>'Top'
            //         ],
            //         [
            //             'title' => 'Iphone 8','price' => '400000',
            //             'buy' => '700000',
            //             'etat'=>'Top'
            //         ],
            //         [
            //             'title' => 'Airpod','price' => '40000',
            //             'buy' => '30000',
            //             'etat'=>'Tendance'
            //         ]
            //     ]
            // ],
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
                // ->setLabel('New')
                // ->setBrand($this->getReference('brand_'.$value))
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
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        // categories
        for($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            $manager->persist($category);
            //articles
            for($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();
                $article->setTitle($faker->sentence())
                        ->setContent($faker->paragraphs(5, true))
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt((new \DateTimeImmutable())->createFromMutable($faker->dateTimeBetween('-6 months')))
                        ->setAuthor("Gugusteh")
                        ->setCategory($category);

                $manager->persist($article);
            
                for($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    $days = (new \Datetime())->diff($article->getCreatedAt())->days;
                    $comment->setAuthor($faker->name)
                            ->setContent($faker->paragraphs(2, true))
                            ->setCreatedAt((new \DateTimeImmutable())->createFromMutable($faker->dateTimeBetween('-'.$days.' days')))
                            ->setArticle($article);
                    
                    $manager->persist($comment);
                }
            }
    
        }

        $manager->flush();
    }
}

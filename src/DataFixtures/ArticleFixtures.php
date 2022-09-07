<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 3; $i++) {
            $article = new Article();
            $article->setTitle("Titre de l'article #$i")
                    ->setContent("<p>Contenu de l'article #$i</p>")
                    ->setImage("https://www.fillmurray.com/240/160")
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setAuthor("Gugusteh");
            $manager->persist($article);
        }

        $manager->flush();
    }
}

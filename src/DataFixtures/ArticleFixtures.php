<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6, true)); // Генерация заголовка
            $article->setText($faker->text(200)); // Генерация текста, ограниченного 200 символами
            $article->setRating($faker->numberBetween(1, 5)); // Генерация рейтинга от 1 до 5

            $manager->persist($article);
        }

        $manager->flush();
    }
}

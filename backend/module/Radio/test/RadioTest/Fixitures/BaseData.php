<?php

namespace RadioTest\Fixitures;

use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Radio\Entity\Episode;
use Radio\Entity\Scheduling;
use Radio\Entity\Show;
use Radio\Entity\Author;
use Radio\Entity\Tag;
use Radio\Entity\TextContent;
use Radio\Entity\Url;
use Radio\Entity\User;
use Radio\Entity\Role;

class BaseData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {

        //$manager->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

        foreach (['Radio\Entity\Role', 'Radio\Entity\Author', 'Radio\Entity\User', 'Radio\Entity\Url', 'Radio\Entity\TextContent', 'Radio\Entity\Show', 'Radio\Entity\Scheduling', 'Radio\Entity\Episode', 'Radio\Entity\Tag'] as $type) {
            $metadata = $manager->getClassMetaData($type);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }



        $r1 = new Role();
        $r1->setId(1);
        $r1->setName("guest");

        $manager->persist($r1);
        $manager->flush();


        $tag = new Tag();
        $tag->setId(1);
        $tag->setName("txag");
        $manager->persist($tag);

        $tag2 = new Tag();
        $tag2->setId(2);
        $tag2->setName("tag2");
        $manager->persist($tag2);


        $r2 = new Role();
        $r2->setId(2);
        $r2->setName("user");
        $r2->setParent($r1);
        $manager->persist($r2);

        $r3 = new Role();
        $r3->setId(3);
        $r3->setName("author");
        $r3->setParent($r2);
        $manager->persist($r3);

        $r4 = new Role();
        $r4->setId(4);
        $r4->setName("admin");
        $r4->setParent($r3);
        $manager->persist($r4);


        $user = new \Radio\Entity\User();
        $user->setId(1);
        $user->setUsername("test");
        $user->setSalt("salto");
        $user->setPassword("NOHASHX");
        $user->setEmail("test@test.hu");
        $user->setRole($r3);
        $manager->persist($user);

        $author = new Author();
        $author->setId(300);
        $author->setAlias("sztyepp");
        $author->setName("DJ. Sztyepp");
        $author->setPhoto("photo.jpg");
        $author->setAvatar("avatar.jpg");
        $author->setIntroduction("uzginuver");
        $author->setUser($manager->find("\Radio\Entity\User", 1));
        $manager->persist($author);

        $user->setAuthor($author);
        $manager->persist($user);


        $a2 = new Author();
        $a2->setId(301);
        $a2->setAlias("a2");
        $a2->setName("Author 2");
        $a2->setPhoto("photo.jpg");
        $a2->setAvatar("avatar.jpg");
        $a2->setIntroduction("author 2 vagzok");
        $a2->setEmail("author2@tilos.hu");
        $manager->persist($a2);


        $link = new Url();
        $link->setId(1);
        $link->setUrl("http://raga.hu");
        $manager->persist($link);

        $l2 = new Url();
        $l2->setId(2);
        $l2->setUrl("http://link2");
        $manager->persist($l2);

        $l3 = new Url();
        $l3->setId(3);
        $l3->setUrl("http://link3");
        $manager->persist($l3);

        $author = new Author();
        $author->setId(763);
        $author->setAlias("szabi");
        $author->setName("Tóth Szabi");
        $author->setPhoto("photo.jpg");
        $author->setAvatar("avatar.jpg");
        $author->setIntroduction("Sangeet Sanstan");
        $author->setUrls([$link]);
        $manager->persist($author);

        $show = new Show();
        $show->setId(1);
        $show->setName("Good show");
        $show->setAlias("goodshow");
        $show->setUrls([$l2, $l3]);
        $show->setType(1);
        $show->setStatus(1);
        $manager->persist($show);

        $page = new TextContent();
        $page->setId(1);
        $page->setAlias("info");
        $page->setContent("Jelentőség:\nNagy");
        $page->setCreated(new \DateTime());
        $page->setModified(new \DateTime());
        $page->setTitle("Infó");
        $page->setType('page');
        $page->setFormat('legacy');
        $page->setAuthor("szabi");
        $page->setTags([$tag]);
        $manager->persist($page);


        $l1 = new TextContent();
        $l1->setId(2);
        $l1->setAlias("");
        $l1->setContent("Episode log pályázatának");
        $l1->setCreated(new \DateTime());
        $l1->setModified(new \DateTime());
        $l1->setTitle("Episode log");
        $l1->setType('log');
        $l1->setFormat('legacy');
        $l1->setAuthor("szabi");
        $l1->setTags([$tag2]);
        $manager->persist($l1);


        $s1 = new Scheduling();
        $s1->setId(1);
        $s1->setValidFrom(new DateTime("2013-01-01 00:00:00"));
        $s1->setValidTo(new DateTime("2013-02-01 00:00:00"));
        $s1->setBase(new DateTime("2013-01-01 00:00:00"));
        $s1->setWeekDay(0);
        $s1->setWeekType(1);
        $s1->setHourFrom(10);
        $s1->setMinFrom(30);
        $s1->setDuration(120);
        $s1->setShow($show);
        $manager->persist($s1);

        $s1 = new Scheduling();
        $s1->setId(2);
        $s1->setValidFrom(new DateTime("2012-01-01 00:00:00"));
        $s1->setValidTo(new DateTime("2013-01-01 00:00:00"));
        $s1->setBase(new DateTime("2013-01-01 00:00:00"));
        $s1->setWeekDay(0);
        $s1->setWeekType(1);
        $s1->setHourFrom(11);
        $s1->setMinFrom(30);
        $s1->setDuration(120);
        $s1->setShow($show);
        $manager->persist($s1);

        $epi = new Episode();
        $epi->setId(1);
        $epi->setPlannedFrom(new DateTime("2013-01-28 10:30:00"));
        $epi->setPlannedTo(new DateTime("2013-01-28 12:30:00"));
        $epi->setRealFrom($epi->getPlannedFrom());
        $epi->setRealTo($epi->getPlannedTo());
        $epi->setShow($show);
        $epi->setText($l1);
        $manager->persist($epi);



        $epi = new Episode();
        $epi->setId(2);
        $epi->setPlannedFrom(new DateTime("2013-01-21 10:30:00"));
        $epi->setPlannedTo(new DateTime("2013-01-21 12:30:00"));
        $epi->setRealFrom($epi->getPlannedFrom());
        $epi->setRealTo($epi->getPlannedTo());
        $epi->setShow($show);
        $manager->persist($epi);





        $manager->flush();

        foreach (['Radio\Entity\Role', 'Radio\Entity\Author', 'Radio\Entity\User', 'Radio\Entity\Url', 'Radio\Entity\TextContent', 'Radio\Entity\Show', 'Radio\Entity\Scheduling', 'Radio\Entity\Episode', 'Radio\Entity\Tag'] as $type) {
            $metadata = $manager->getClassMetaData($type);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
        }
    }


}
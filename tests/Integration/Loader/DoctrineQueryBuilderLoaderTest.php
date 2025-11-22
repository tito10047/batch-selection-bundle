<?php

namespace Tito10047\BatchSelectionBundle\Tests\Integration\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Tito10047\BatchSelectionBundle\Loader\DoctrineQueryBuilderLoader;
use Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src\Entity\RecordInteger;
use Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src\Factory\RecordIntegerFactory;
use Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src\Factory\TestCategoryFactory;
use Tito10047\BatchSelectionBundle\Tests\Integration\Kernel\AssetMapperKernelTestCase;

class DoctrineQueryBuilderLoaderTest extends AssetMapperKernelTestCase
{
    public function testBasic(): void
    {
        $records = RecordIntegerFactory::createMany(10);

        $loader = new DoctrineQueryBuilderLoader();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder()
            ->select('i')
            ->from(RecordInteger::class, 'i')
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(5);

        $this->assertTrue($loader->supports($qb));
        $this->assertEquals(10, $loader->getTotalCount($qb));

        $ids = array_map(fn(RecordInteger $record) => $record->getId(), $records);
        sort($ids);
        $foundIds = $loader->loadAllIdentifiers(null, $qb, 'id');
        sort($foundIds);

        $this->assertEquals($ids, $foundIds);
    }

    public function testWithWhere(): void
    {
        $records = RecordIntegerFactory::createMany(10);

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine')->getManager();

		$expectedIds = array_values(array_map(
			fn(RecordInteger $r) => $r->getId(),
			array_filter($records, fn(RecordInteger $r) => $r->getName()=='keep', ARRAY_FILTER_USE_BOTH)
		));

        $loader = new DoctrineQueryBuilderLoader();

        $qb = $em->createQueryBuilder()
            ->select('i')
            ->from(RecordInteger::class, 'i')
            ->where('i.name = :name')
            ->setParameter('name', 'keep')
            ->orderBy('i.id', 'DESC')
            ->setFirstResult(2)
            ->setMaxResults(3);

        $this->assertTrue($loader->supports($qb));
        $this->assertEquals(count($expectedIds), $loader->getTotalCount($qb));


        sort($expectedIds);

        $foundIds = $loader->loadAllIdentifiers(null, $qb, 'id');
        sort($foundIds);

        $this->assertEquals($expectedIds, $foundIds);
    }

    public function testWithJoin(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine')->getManager();

		TestCategoryFactory::createOne([
			"name"=>"A"
		]);
		TestCategoryFactory::createOne([
			"name"=>"A"
		]);

		$records = RecordIntegerFactory::createMany(10);
        $loader = new DoctrineQueryBuilderLoader();

		$expectedIds = array_values(array_map(
			fn(RecordInteger $r) => $r->getId(),
			array_filter($records, fn(RecordInteger $r) => $r->getCategory()->getName()=='A', ARRAY_FILTER_USE_BOTH)
		));

        $qb = $em->createQueryBuilder()
            ->select('i')
            ->from(RecordInteger::class, 'i')
            ->join('i.category', 'c')
            ->where('c.name = :name')
            ->setParameter('name', 'A')
            ->orderBy('i.id', 'DESC')
            ->setFirstResult(1)
            ->setMaxResults(2);

        $this->assertTrue($loader->supports($qb));
        $this->assertEquals(count($expectedIds), $loader->getTotalCount($qb));

        $foundIds = $loader->loadAllIdentifiers(null, $qb, 'id');
        sort($foundIds);

        $this->assertEquals($expectedIds, $foundIds);
    }
}

<?php

namespace Tito10047\BatchSelectionBundle\Tests\Integration\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use Tito10047\BatchSelectionBundle\Loader\DoctrineCollectionLoader;
use Tito10047\BatchSelectionBundle\Normalizer\ObjectNormalizer;
use Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src\Entity\RecordInteger;
use Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src\Factory\RecordIntegerFactory;
use Tito10047\BatchSelectionBundle\Tests\Integration\Kernel\AssetMapperKernelTestCase;

class DoctrineCollectionLoaderTest extends AssetMapperKernelTestCase
{
    public function testBasic(): void
    {
        $records = RecordIntegerFactory::createMany(10);

        // jednoduchý resolver iba pre potreby testu
        $resolver = new ObjectNormalizer();
        $collection = new ArrayCollection($records);
        $loader = new DoctrineCollectionLoader($resolver);

        $this->assertTrue($loader->supports($collection));
        $this->assertSame(10, $loader->getTotalCount($collection));

        $ids = array_map(fn(RecordInteger $record) => $record->getId(), $records);
        $foundIds = $loader->loadAllIdentifiers($collection);

        $this->assertEquals($ids, $foundIds);
    }
}

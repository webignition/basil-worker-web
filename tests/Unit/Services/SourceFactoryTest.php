<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services;

use App\Exception\MissingTestSourceException;
use App\Model\Manifest;
use App\Model\UploadedSourceCollection;
use App\Services\SourceFactory;
use App\Services\SourceFileStore;
use App\Tests\Mock\Model\MockUploadedSourceCollection;
use PHPUnit\Framework\TestCase;
use webignition\BasilWorker\PersistenceBundle\Services\Factory\SourceFactory as BundleSourceFactory;

class SourceFactoryTest extends TestCase
{
    /**
     * @dataProvider createCollectionFromManifestThrowsExceptionDataProvider
     */
    public function testCreateCollectionFromManifestThrowsException(UploadedSourceCollection $uploadedSources): void
    {
        $factory = new SourceFactory(
            \Mockery::mock(SourceFileStore::class),
            \Mockery::mock(BundleSourceFactory::class)
        );

        $path = 'Test/test.yml';

        $manifest = \Mockery::mock(Manifest::class);
        $manifest
            ->shouldReceive('getTestPaths')
            ->andReturn([
                $path,
            ]);

        self::expectExceptionObject(new MissingTestSourceException($path));

        $factory->createCollectionFromManifest($manifest, $uploadedSources);
    }

    /**
     * @return array[]
     */
    public function createCollectionFromManifestThrowsExceptionDataProvider(): array
    {
        return [
            'source missing' => [
                'uploadedSources' => (new MockUploadedSourceCollection())
                    ->withContainsCall('Test/test.yml', false)
                    ->getMock(),
            ],
            'source invalid' => [
                'uploadedSources' => (new MockUploadedSourceCollection())
                    ->withContainsCall('Test/test.yml', true)
                    ->withOffsetGetCall('Test/test.yml', null)
                    ->getMock(),
            ],
        ];
    }
}

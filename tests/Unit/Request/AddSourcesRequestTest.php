<?php

declare(strict_types=1);

namespace App\Tests\Unit\Request;

use App\Model\Manifest;
use App\Model\UploadedFileKey;
use App\Model\UploadedSource;
use App\Model\UploadedSourceCollection;
use App\Request\AddSourcesRequest;
use App\Tests\Mock\MockUploadedFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AddSourcesRequestTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        Request $request,
        ?Manifest $expectedManifest,
        UploadedSourceCollection $expectedSources
    ): void {
        $addSourcesRequest = new AddSourcesRequest($request);

        self::assertEquals($expectedManifest, $addSourcesRequest->getManifest());
        self::assertEquals($expectedSources, $addSourcesRequest->getUploadedSources());
    }

    /**
     * @return array[]
     */
    public function createDataProvider(): array
    {
        $manifestUploadedFile = (new MockUploadedFile())->getMock();
        $source1 = (new MockUploadedFile())->getMock();
        $source2 = (new MockUploadedFile())->getMock();
        $source3 = (new MockUploadedFile())->getMock();

        $manifestKey = new UploadedFileKey(AddSourcesRequest::KEY_MANIFEST);

        return [
            'empty' => [
                'request' => new Request(),
                'expectedManifest' => null,
                'expectedSources' => new UploadedSourceCollection(),
            ],
            'manifest present only' => [
                'request' => new Request(
                    [],
                    [],
                    [],
                    [],
                    [
                        $manifestKey->encode() => $manifestUploadedFile,
                    ]
                ),
                'expectedManifest' => new Manifest($manifestUploadedFile),
                'expectedSources' => new UploadedSourceCollection(),
            ],
            'sources present only' => [
                'request' => new Request(
                    [],
                    [],
                    [],
                    [],
                    [
                        (new UploadedFileKey('test1.yml'))->encode() => $source1,
                        (new UploadedFileKey('test2.yml'))->encode() => $source2,
                        (new UploadedFileKey('test3.yml'))->encode() => $source3,
                    ]
                ),
                'expectedManifest' => null,
                'expectedSources' => new UploadedSourceCollection([
                    new UploadedSource('test1.yml', $source1),
                    new UploadedSource('test2.yml', $source2),
                    new UploadedSource('test3.yml', $source3),
                ]),
            ],
            'manifest and sources present' => [
                'request' => new Request(
                    [],
                    [],
                    [],
                    [],
                    [
                        $manifestKey->encode() => $manifestUploadedFile,
                        (new UploadedFileKey('test1.yml'))->encode() => $source1,
                        (new UploadedFileKey('test2.yml'))->encode() => $source2,
                        (new UploadedFileKey('test3.yml'))->encode() => $source3,
                    ]
                ),
                'expectedManifest' => new Manifest($manifestUploadedFile),
                'expectedSources' => new UploadedSourceCollection([
                    new UploadedSource('test1.yml', $source1),
                    new UploadedSource('test2.yml', $source2),
                    new UploadedSource('test3.yml', $source3),
                ]),
            ],
        ];
    }
}

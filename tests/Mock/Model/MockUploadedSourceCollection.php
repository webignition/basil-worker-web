<?php

declare(strict_types=1);

namespace App\Tests\Mock\Model;

use App\Model\UploadedSourceCollection;
use Mockery\MockInterface;

class MockUploadedSourceCollection
{
    /**
     * @var UploadedSourceCollection|MockInterface
     */
    private UploadedSourceCollection $sources;

    public function __construct()
    {
        $this->sources = \Mockery::mock(UploadedSourceCollection::class);
    }

    public function getMock(): UploadedSourceCollection
    {
        return $this->sources;
    }

    public function withContainsCall(string $path, bool $contains): self
    {
        $this->sources
            ->shouldReceive('contains')
            ->with($path)
            ->andReturn($contains);

        return $this;
    }

    /**
     * @param mixed $return
     */
    public function withOffsetGetCall(string $offset, $return): self
    {
        $this->sources
            ->shouldReceive('offsetGet')
            ->with($offset)
            ->andReturn($return);

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional\Services;

use App\Tests\AbstractBaseFunctionalTest;
use webignition\StringPrefixRemover\DefinedStringPrefixRemover;

class StringPrefixRemoverTest extends AbstractBaseFunctionalTest
{
    private const COMPILER_SOURCE_DIRECTORY = '/app/source';
    private const COMPILER_TARGET_DIRECTORY = '/app/tests';

    private DefinedStringPrefixRemover $compilerSourceRemover;
    private DefinedStringPrefixRemover $compilerTargetRemover;

    protected function setUp(): void
    {
        parent::setUp();

        $compilerSourceRemover = self::$container->get('app.services.path-prefix-remover.compiler-source');
        if ($compilerSourceRemover instanceof DefinedStringPrefixRemover) {
            $this->compilerSourceRemover = $compilerSourceRemover;
        }

        $compilerTargetRemover = self::$container->get('app.services.path-prefix-remover.compiler-target');
        if ($compilerTargetRemover instanceof DefinedStringPrefixRemover) {
            $this->compilerTargetRemover = $compilerTargetRemover;
        }
    }

    /**
     * @dataProvider compilerSourceRemoverDataProvider
     */
    public function testCompilerSourceRemover(string $path, string $expectedPath)
    {
        self::assertSame($expectedPath, $this->compilerSourceRemover->remove($path));
    }

    public function compilerSourceRemoverDataProvider(): array
    {
        return [
            'path shorter than prefix' => [
                'path' => 'short/path',
                'expectedPath' => 'short/path',
            ],
            'prefix not present' => [
                'path' => '/path/that/does/not/contain/prefix/test.yml',
                'expectedPath' => '/path/that/does/not/contain/prefix/test.yml',
            ],
            'prefix present' => [
                'path' => self::COMPILER_SOURCE_DIRECTORY . '/Test/test.yml',
                'expectedPath' => 'Test/test.yml',
            ],
        ];
    }

    /**
     * @dataProvider compilerTargetRemoverDataProvider
     */
    public function testCompilerTargetRemover(string $path, string $expectedPath)
    {
        self::assertSame($expectedPath, $this->compilerTargetRemover->remove($path));
    }

    public function compilerTargetRemoverDataProvider(): array
    {
        return [
            'path shorter than prefix' => [
                'path' => 'short/path',
                'expectedPath' => 'short/path',
            ],
            'prefix not present' => [
                'path' => '/path/that/does/not/contain/prefix/GeneratedTest.php',
                'expectedPath' => '/path/that/does/not/contain/prefix/GeneratedTest.php',
            ],
            'prefix present' => [
                'path' => self::COMPILER_TARGET_DIRECTORY . '/GeneratedTest.php',
                'expectedPath' => 'GeneratedTest.php',
            ],
        ];
    }
}

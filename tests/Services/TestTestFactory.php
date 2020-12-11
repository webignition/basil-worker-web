<?php

declare(strict_types=1);

namespace App\Tests\Services;

use webignition\BasilCompilerModels\TestManifest;
use webignition\BasilModels\Test\Configuration as ModelTestConfiguration;
use webignition\BasilWorker\PersistenceBundle\Entity\Test;
use webignition\BasilWorker\PersistenceBundle\Entity\TestConfiguration;
use webignition\BasilWorker\PersistenceBundle\Services\EntityPersister;
use webignition\BasilWorker\PersistenceBundle\Services\Factory\TestFactory;

class TestTestFactory
{
    private TestFactory $testFactory;
    private EntityPersister $entityPersister;

    public function __construct(TestFactory $testFactory, EntityPersister $entityPersister)
    {
        $this->testFactory = $testFactory;
        $this->entityPersister = $entityPersister;
    }

    /**
     * @param TestConfiguration $configuration
     * @param string $source
     * @param string $target
     * @param int $stepCount
     * @param Test::STATE_* $state
     *
     * @return Test
     */
    public function create(
        TestConfiguration $configuration,
        string $source,
        string $target,
        int $stepCount,
        string $state = Test::STATE_AWAITING
    ): Test {
        $tests = $this->createFromManifestCollection([
            new TestManifest(
                new ModelTestConfiguration($configuration->getBrowser(), $configuration->getUrl()),
                $source,
                $target,
                $stepCount
            ),
        ]);

        $test = $tests[0];
        $test->setState($state);
        $this->entityPersister->persist($test);

        return $test;
    }

    /**
     * @param TestManifest[] $manifests
     *
     * @return Test[]
     */
    public function createFromManifestCollection(array $manifests): array
    {
        $tests = [];

        foreach ($manifests as $manifest) {
            if ($manifest instanceof TestManifest) {
                $tests[] = $this->createFromManifest($manifest);
            }
        }

        return $tests;
    }

    private function createFromManifest(TestManifest $manifest): Test
    {
        $manifestConfiguration = $manifest->getConfiguration();

        return $this->testFactory->create(
            TestConfiguration::create(
                $manifestConfiguration->getBrowser(),
                $manifestConfiguration->getUrl()
            ),
            $manifest->getSource(),
            $manifest->getTarget(),
            $manifest->getStepCount()
        );
    }
}

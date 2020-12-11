<?php

declare(strict_types=1);

namespace App\Tests\Functional\MessageDispatcher;

use App\Event\JobReadyEvent;
use App\Message\JobReadyMessage;
use App\MessageDispatcher\JobReadyMessageDispatcher;
use App\Tests\AbstractBaseFunctionalTest;
use App\Tests\Services\Asserter\MessengerAsserter;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use webignition\SymfonyTestServiceInjectorTrait\TestClassServicePropertyInjectorTrait;

class JobReadyMessageDispatcherTest extends AbstractBaseFunctionalTest
{
    use MockeryPHPUnitIntegration;
    use TestClassServicePropertyInjectorTrait;

    private JobReadyMessageDispatcher $messageDispatcher;
    private EventDispatcherInterface $eventDispatcher;
    private MessengerAsserter $messengerAsserter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->injectContainerServicesIntoClassProperties();
    }


    public function testDispatch()
    {
        $this->messengerAsserter->assertQueueIsEmpty();

        $this->messageDispatcher->dispatch();

        $this->messengerAsserter->assertQueueCount(1);
        $this->messengerAsserter->assertMessageAtPositionEquals(0, new JobReadyMessage());
    }

    /**
     * @dataProvider subscribesToEventDataProvider
     */
    public function testSubscribesToEvent(Event $event, JobReadyMessage $expectedQueuedMessage)
    {
        $this->messengerAsserter->assertQueueIsEmpty();

        $this->eventDispatcher->dispatch($event);

        $this->messengerAsserter->assertQueueCount(1);
        $this->messengerAsserter->assertMessageAtPositionEquals(
            0,
            $expectedQueuedMessage
        );
    }

    public function subscribesToEventDataProvider(): array
    {
        return [
            JobReadyEvent::class => [
                'event' => new JobReadyEvent(),
                'expectedQueuedMessage' => new JobReadyMessage(),
            ],
        ];
    }
}

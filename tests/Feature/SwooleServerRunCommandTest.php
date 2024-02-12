<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Feature;

use SwooleBundle\SwooleBundle\Client\HttpClient;
use SwooleBundle\SwooleBundle\Tests\Fixtures\Symfony\TestBundle\Test\ServerTestCase;

final class SwooleServerRunCommandTest extends ServerTestCase
{
    protected function setUp(): void
    {
        $this->markTestSkippedIfXdebugEnabled();
        $this->deleteVarDirectory();
    }

    public function testRunAndCall(): void
    {
        $serverRun = $this->createConsoleProcess([
            'swoole:server:run',
            '--host=localhost',
            '--port=9999',
        ]);

        $serverRun->setTimeout(10);
        $serverRun->start();

        $this->runAsCoroutineAndWait(function (): void {
            $client = HttpClient::fromDomain('localhost', 9999, false);
            $this->assertTrue($client->connect(3, 1, true));

            $this->assertHelloWorldRequestSucceeded($client);
        });

        $serverRun->stop();
    }

    public function testRunAndCallOnReactorRunningMode(): void
    {
        $serverRun = $this->createConsoleProcess([
            'swoole:server:run',
            '--host=localhost',
            '--port=9999',
        ], ['APP_ENV' => 'reactor']);

        $serverRun->setTimeout(10);
        $serverRun->start();

        $this->runAsCoroutineAndWait(function (): void {
            $client = HttpClient::fromDomain('localhost', 9999, false);
            $this->assertTrue($client->connect(3, 1, true));

            $this->assertHelloWorldRequestSucceeded($client);
        });

        $serverRun->stop();
    }

    public function testRunAndSigIntTerminationDoesNotThrowExitException(): void
    {
        $serverRun = $this->createConsoleProcess([
            'swoole:server:run',
            '--host=localhost',
            '--port=9999',
        ]);

        $serverRun->setTimeout(10);
        $serverRun->enableOutput();
        $serverRun->start();

        sleep(1);

        $this->killProcessUsingSignal($serverRun->getPid(), SIGINT);
        $output = $serverRun->getOutput();

        // I wasn't able to simulate, how to get output of child processes and test it,
        // so this assertion is kind of dummy for now
        $this->assertStringNotContainsString('ExitException', $output);
    }
}

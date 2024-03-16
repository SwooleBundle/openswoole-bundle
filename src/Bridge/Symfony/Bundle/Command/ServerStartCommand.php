<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\Bundle\Command;

use SwooleBundle\SwooleBundle\Bridge\Symfony\Bundle\Exception\CouldNotCreatePidFileException;
use SwooleBundle\SwooleBundle\Bridge\Symfony\Bundle\Exception\PidFileNotAccessibleException;
use SwooleBundle\SwooleBundle\Server\HttpServer;
use SwooleBundle\SwooleBundle\Server\HttpServerConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

use function SwooleBundle\SwooleBundle\get_object_property;

final class ServerStartCommand extends ServerExecutionCommand
{
    protected function configure(): void
    {
        $this->setDescription('Run Swoole HTTP server in the background.')
            ->addOption(
                'pid-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Pid file',
                $this->getProjectDirectory() . '/var/swoole.pid'
            );

        parent::configure();
    }

    protected function prepareServerConfiguration(
        HttpServerConfiguration $serverConfiguration,
        InputInterface $input,
    ): void {
        /** @var string|null $pidFile */
        $pidFile = $input->getOption('pid-file');
        $serverConfiguration->daemonize($pidFile);

        parent::prepareServerConfiguration($serverConfiguration, $input);
    }

    protected function startServer(
        HttpServerConfiguration $serverConfiguration,
        HttpServer $server,
        SymfonyStyle $io,
    ): void {
        $pidFile = $serverConfiguration->getPidFile();

        if (!touch($pidFile)) {
            throw PidFileNotAccessibleException::forFile($pidFile);
        }

        if (!is_writable($pidFile)) {
            throw CouldNotCreatePidFileException::forPath($pidFile);
        }

        $server->start();

        $this->closeSymfonyStyle($io);
    }

    private function closeSymfonyStyle(SymfonyStyle $io): void
    {
        $output = get_object_property($io, 'output', OutputStyle::class);

        if ($output instanceof ConsoleOutput) {
            $this->closeConsoleOutput($output);
        } elseif ($output instanceof StreamOutput) {
            $this->closeStreamOutput($output);
        }
    }

    /**
     * Prevents usage of php://stdout or php://stderr while running in background.
     */
    private function closeConsoleOutput(ConsoleOutput $output): void
    {
        fclose($output->getStream());

        /** @var StreamOutput $streamOutput */
        $streamOutput = $output->getErrorOutput();

        $this->closeStreamOutput($streamOutput);
    }

    private function closeStreamOutput(StreamOutput $output): void
    {
        // @phpstan-ignore-next-line - proper constant from OutputInterface does not work properly
        $output->setVerbosity(PHP_INT_MIN);
        fclose($output->getStream());
    }
}

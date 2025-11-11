<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(name: 'app:list-routes', description: 'List all available API routes')]
class ListRoutesCommand extends Command
{
    public function __construct(private RouterInterface $router)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $routes = $this->router->getRouteCollection();

        $output->writeln('<info>Available API Routes:</info>');
        $output->writeln('==========================');

        foreach ($routes as $routeName => $route) {
            $path = $route->getPath();
            if (str_starts_with($path, '/api')) {
                $methods = $route->getMethods() ? implode(', ', $route->getMethods()) : 'ANY';
                $output->writeln(sprintf('<comment>%s</comment> %s -> %s', $methods, $path, $routeName));
            }
        }

        return Command::SUCCESS;
    }
}

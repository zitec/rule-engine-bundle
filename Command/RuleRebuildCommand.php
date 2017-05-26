<?php

namespace Zitec\RuleEngineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RuleRebuildCommand
 *
 * @package RuleEngineBundle\Command
 */
class RuleRebuildCommand extends ContainerAwareCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('rule-engine:rebuild-rules')
            ->setDescription('Rebuild expressions for all rules')
            ->setHelp('This command allows rebuilding of all expressions generated from JSON data, and should be used after changing expression generation functions.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Rebuilding rule expressions from JSON data...');

        $errors = $this->getContainer()->get('data_importer.parse_airlines')->saveEntities();

        foreach ($errors as $key => $value) {
            if (is_int($key)) {
                $output->writeln("<info>$value</info>");
            } else {
                $output->writeln("<error>$key: $value </error>");
            }
        }

        return null;
    }
}

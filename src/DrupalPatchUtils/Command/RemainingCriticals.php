<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\RemainingCriticals.
 */

namespace DrupalPatchUtils\Command;

use Goutte\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemainingCriticals extends CommandBase {

    /**
     * The URL having the amount of criticals.
     */
    CONST DRUPAL_ORG_8_PAGE = 'https://www.drupal.org/drupal-8.0';

    protected function configure()
    {
        $this
          ->setName('remainingCriticals')
          ->setDescription('Shows the remaining criticals of Drupal 8');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $client = new Client();
        $crawler = $client->request('GET', static::DRUPAL_ORG_8_PAGE);

        $amount_criticals = $crawler->filter('#block-drupalorg-project-critical-count h3 a')->text();

        $output->writeln(sprintf('%s', $amount_criticals));
    }

}

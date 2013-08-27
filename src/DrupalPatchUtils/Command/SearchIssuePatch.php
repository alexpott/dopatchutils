<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 09/08/2013
 * Time: 02:42
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\IssueInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Class SearchIssuePatch
 * @package DrupalPatchUtils\Command
 */
class SearchIssuePatch extends Command {

  protected function configure()
  {
    $this
    ->setName('searchIssuePatch')
    ->setDescription('Searches a d.o drupal 8 issue patch for text')
    ->addArgument(
      'url',
      InputArgument::REQUIRED,
      'What is the url of the issue to retrieve?'
    )
    ->addArgument(
      'searchText',
      InputArgument::REQUIRED,
      'What is the text to search for?'
    )
    ->addOption(
      'regex',
        null,
        InputOption::VALUE_NONE,
        'If set use preg_match to search patch'
      );

  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $regex = $input->getOption('regex');
    $search_text = $input->getArgument('searchText');
    $request = new Request();
    $request->setUri($input->getArgument('url') . '/project-issue/json');

    $client = new Client();
    $response = $client->dispatch($request);

    if ($response->isSuccess()) {
      $issue = new IssueInfo($response->getBody());
      $patches_to_search = $issue->getLatestPatch();
      if (count($patches_to_search) > 1) {
        // Need to choose patch.
        $app = $this->getApplication();
        $dialog = $app->getHelperSet()->get('dialog');
        $output->writeln('Multiple patches detected:');
        $output->writeln($this->getChoices($patches_to_search));

        $patch_key = $dialog->askAndValidate($output, 'Choose patch to search: ', function ($patch_key) use ($patches_to_search) {
          if (!in_array($patch_key, range(0 ,count($patches_to_search)))) {
            throw new \InvalidArgumentException(sprintf('Choice "%s" is invalid.', $patch_key));
          }
          return $patch_key;
        }, false, 1);
        $patch = $patches_to_search[$patch_key - 1];
      }
      elseif (count($patches_to_search) == 1) {
        $patch = $patches_to_search[0];
      }
      else {
        // Nothing to do.
        $output->writeln("No patches availabe to search at ". $request->getUriString());
        return;
      }

      if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
        $output->writeln('Searching ' . $patch);
      }

      $request = new Request();
      $request->setUri($patch);

      $client = new Client();
      $response = $client->dispatch($request);
      $body = $response->getBody();
      if ($regex) {
        $found = preg_match($search_text, $body);
      }
      else {
        $found = strpos($body, $search_text);
      }

      if ($found) {
        $output->writeln('<fg=green>Found text "'. $search_text .'" in '. $issue->getUri() . '</fg=green>');
      }
    }
  }

  /**
   * Helper function to convert patch list into numbered list.
   *
   * @param array $patches_to_search
   * @return array
   */
  protected function getChoices(array $patches_to_search) {
    return array_map(function ($value) {
      static $counter = 1;
      $value = '['. $counter .'] ' . $value;
      if ($counter == 1) {
        $value .= ' (default)';
      }
      $counter++;
      return $value;
    }, $patches_to_search);
  }
}
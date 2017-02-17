<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BulkIssueCredit extends CommandBase {

  const MAX_LENGTH = 128;

  protected function configure()
  {
    $this
      ->setName('bulkIssueCredit')
      ->setAliases(array('bic'))
      ->setDescription('Adds issue credit for a very long list')
      ->addArgument(
        'url',
        InputArgument::REQUIRED,
        'What is the url of the issue to add credit to?'
      )
      ->addArgument(
        'credit_file',
        InputArgument::REQUIRED,
        'What is the path of the comma delimted d.o user names to credit?'
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $issue = $this->getIssue($input->getArgument('url'));
    $credit_file_path = $input->getArgument('credit_file');
    if (!file_exists($credit_file_path)) {
      throw new \Exception(sprintf('The file "%s" does not exist.', $credit_file_path));
    }
    if ($issue) {
      $browser = $this->login($input, $output);
      foreach ($this->getCreditTexts($credit_file_path) as $credit_text) {
        $comment_form = $browser->getCommentForm($issue->getUri());
        $comment_form->addIssueCredit($credit_text);
        $browser->submitForm($comment_form->getForm());
      }
    }
  }

  /**
   * Reads a file of issue credits to add to an issue.
   *
   * @param string $file_path
   *
   * @return array
   */
  protected function getCreditTexts($file_path) {
    // Remove all new lines and replace any double commas with a single one.
    $credit = file_get_contents($file_path);
    $credit = str_replace(array("\r", "\n"), ',', $credit);
    $credit = preg_replace('/,+/', ',', $credit);
    // Trim the string and include commas.
    $credit = trim($credit, " \t\n\r\0\x0B,");
    $credit_texts = [];
    // Chop the string up into an array of things to post since the add_credit
    // field only accept static::MAX_LENGTH characters.
    $length = mb_strlen($credit);
    while ($length > 0) {
      if ($length > static::MAX_LENGTH) {
        $chunk = mb_substr($credit, 0, static::MAX_LENGTH);
        // Find the last comma in the chunk.
        $chunk = mb_substr($chunk, 0, mb_strrpos($chunk, ','));
      }
      else {
        // Get the last set of credit to add.
        $chunk = $credit;
      }

      // Remove the chunk from the original credit.
      $credit = mb_substr($credit, mb_strlen($chunk));
      $credit_texts[] = $chunk;
      // Trim and get the new length.
      $credit = trim($credit, " \t\n\r\0\x0B,");
      $length = mb_strlen($credit);
    }
    return $credit_texts;
  }

}

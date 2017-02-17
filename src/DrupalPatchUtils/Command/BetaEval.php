<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\BetaEval.
 */

namespace DrupalPatchUtils\Command;

use Goutte\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class BetaEval extends CommandBase
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
          ->setName('betaEval')
          ->setAliases(['ba'])
          ->setDescription('Provides some beta evalutation');
    }

    protected function fetchTemplate() {
        $goutte = new Client();
        $crawler = $goutte->request('GET', 'https://www.drupal.org/node/2373483');

        $beta_evaluation = (string) $crawler->filter('div.codeblock code')->getNode(0)->nodeValue;
        return $beta_evaluation;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $template = $this->fetchTemplate();
        $template = str_replace('<!--<', '<', $template);
        $template = str_replace('>-->', '>', $template);

        $category_placeholder = 'Bug/Task/Feature because ...';
        $priority_placeholder = 'Major because ... Critical/Not critical because ... ';
        $prioritized_placeholder = 'The main goal of this issue is usability/accessibility/security/performance/removing code already deprecated for 8.0.0/Migrate. (Which? Specify.)';
        $disruption_placeholder = 'Disruptive for core/contributed and custom modules/themes because it will require a BC break/deprecation/data model changes/an upgrade path/internal refactoring/widespread changes... (Which? Specify.)';

        $helper = $this->getHelper('question');
        $options = array(
          'cl' => 'cleanup',
          'pn' => 'performance-normal',
          'pm' => 'performance-major',
          'cu' => 'custom',
        );
        $question = new ChoiceQuestion(
            'Choose one of the following templates',
            $options
        );
        $answer = $helper->ask($input, $output, $question);
        print_r($answer);

        $string = '';
        switch ($answer) {
            case 'cu':
                $string = strtr($template, [
                  $category_placeholder => $helper->ask($input, $output, new Question('Category?')),
                  $priority_placeholder => $helper->ask($input, $output, new Question('Priority?')),
                  $prioritized_placeholder => $helper->ask($input, $output, new Question('Prioritized?')),
                  $disruption_placeholder => $helper->ask($input, $output, new Question('Disruption?')),
                ]);
                break;
            case 'cl':
                $string = strtr($template, [
                    $category_placeholder => 'Task because it is a cleanup task.',
                    $priority_placeholder => 'Normal, because the impact is not 0, just a small step.',
                    $prioritized_placeholder => 'Nope',
                    $disruption_placeholder => 'None',
                ]);
                break;
            case 'pn':
                $string = strtr($template, [
                  $category_placeholder => 'Task|Bug because it is a performance improvement',
                  $priority_placeholder => 'Normal, because the impact is not 0, just a small step.',
                  $prioritized_placeholder => 'Nope',
                  $disruption_placeholder => 'None',
                ]);
                break;
            case 'pm':
                $string = strtr($template, [
                  $category_placeholder => 'Task|Bug because it is a performance improvement',
                  $priority_placeholder => 'Major, because it saves actually something',
                  $prioritized_placeholder => 'Nope',
                  $disruption_placeholder => 'None',
                ]);
                break;
        }

        $output->write($string);
        return;
    }

}

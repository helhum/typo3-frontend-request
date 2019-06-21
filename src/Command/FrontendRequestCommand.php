<?php
declare(strict_types=1);
namespace Helhum\Typo3FrontendRequest\Command;

use Helhum\Typo3FrontendRequest\Typo3Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Http\Request;

class FrontendRequestCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure()
    {
        $this
            ->addOption(
                'url',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Frontend request URL'
            )
            ->addOption(
                'user',
                null,
                InputOption::VALUE_REQUIRED,
                'UID of frontend user to create a session for'
            )
            ->setDescription('Requests given TYPO3 frontend URLs')
            ->setHelp('Requests all given URLs with given authentication');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($input->getOption('url'))) {
            $output->writeln('At least one URL is required');
            return 1;
        }
        $typo3Client = new Typo3Client();
        foreach ($input->getOption('url') as $url) {
            $request = new Request(
                $url,
                'GET',
                'php://input',
                [
                    'User-Agent' => $GLOBALS['TYPO3_CONF_VARS']['HTTP']['headers']['User-Agent'] ?? 'TYPO3'
                ]
            );
            if ($user = $input->getOption('user')) {
                $request = $request->withHeader('x-typo3-frontend-user', $user);
            }

            $response = $typo3Client->send($request);

            $output = (string)$response->getBody();
        }

        return 0;
    }
}

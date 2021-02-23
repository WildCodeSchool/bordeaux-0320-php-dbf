<?php


namespace App\BackUpService;


use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use \DateTime;

class BackupCommand extends Command
{
    private string $folder;
    private $dropboxService;

    public function __construct(string $name = null, KernelInterface $kernel, DropBoxService $dropboxService)
    {
        $this->folder = $kernel->getProjectDir() . '/backup';
        $this->dropboxService = $dropboxService;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('easyauto:backup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('backup-manager:backup');

        $date = new DateTime('now');
        $h = $date->format('H');

        if (is_file($this->folder . '/backup-' . $h . '.sql.gz')) {
            unlink($this->folder . '/backup-' . $h . '.sql.gz');
        }

        $arguments = [
            'database' => 'development',
            'destinations' => ['local'],
            '-c'     => 'gzip',
            '--filename'  => 'backup-' . $h . '.sql',
        ];

        $in = new ArrayInput($arguments);
        $returnCode = $command->run($in, $output);
/*
        try {
            $this->dropboxService->removeFile('', 'backup-' . $h . '.sql.gz');
        } catch (Exception $e) {

        } finally {
            $this->dropboxService->sendFile($this->folder . '/backup-' . $h . '.sql.gz');
            unlink($this->folder . '/backup-' . $h . '.sql.gz');
        }
*/
        return Command::SUCCESS;
    }

}

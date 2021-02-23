<?php


namespace App\BackUpService;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use BackupManager\Manager;
use Symfony\Component\HttpKernel\KernelInterface;

class RestoreBackupCommand extends Command
{

    const FOLDER = 'backup';

    private $backupManager;
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    public function __construct(Manager $backupManager, KernelInterface $kernel) {
        parent::__construct();
        $this->backupManager = $backupManager;
        $this->kernel = $kernel;
    }

    public function configure()
    {
        $this
            ->setName('easyauto:restore')
            ->addArgument('file', InputArgument::REQUIRED, 'Nom du fichier de sauvegarde ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');

        $this->backupManager->makeRestore()->run('local', $file, 'development', 'gzip');

        $io->writeln('votre fichier est ' . $file);

        return self::SUCCESS;
    }
}

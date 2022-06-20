<?php
namespace App\Command;

use App\Repository\MatchesRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MatchEventCommandCommand extends Command
{
    private $matchesRepository;
    private $activeMatches;

    protected static $defaultName = 'app:matches:event';

    public function __construct(MatchesRepository $matchesRepository)
    {
        $this->matchesRepository = $matchesRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this->activeMatches = $this->matchesRepository->findAllActiveMatches();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->activeMatches as $activeMatch){

            $todayTime = new \DateTime();
            $matchDate = new \DateTime($activeMatch->getMatchDate()->format('Y-m-d H:i:s'));
            $matchDate->modify("+10 Minutes");

            if($todayTime->getTimestamp() > $matchDate->getTimestamp()){
                $this->matchesRepository->endMatch($activeMatch->getId());
            }

            $this->matchesRepository->matchRandomEvent($activeMatch->getId());

        }

        $io->success(sprintf('Events processed for active matches.'));

        return 0;
    }
}
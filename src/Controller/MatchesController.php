<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\MatchEvents;
use App\Entity\Teams;
use App\Repository\MatchesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class MatchesController extends AbstractController
{
    const NOT_STARTED = 0;
    const MATCH_STARTED = 1;
    const FINISHED = 2;

    #[Route('/matches', name: 'app_matches')]
    public function index(): Response
    {
        return $this->render('matches/index.html.twig', [
            'controller_name' => 'MatchesController',
        ]);
    }

    #[Route('/matches/match', name: 'matchData')]
    public function matchDataAction(ManagerRegistry $doctrine, Request $request): Response
    {
        if(empty($request->get('matchId'))){
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'Match Id Missing'),
                400);
        }

        $data = [];
        $matchId = $request->get('matchId');
        $matchData = $doctrine->getRepository(Matches::class)->find($matchId);
        if (!$matchData) {
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'No Match Found For Id: '.$matchId),
                400);
        }

        $match = $doctrine->getRepository(Matches::class);

        $todayTime = new \DateTime();
        $matchDate = new \DateTime($matchData->getMatchDate()->format('Y-m-d H:i:s'));
        $matchDate->modify("+10 Minutes");
        $dayDiff = $matchDate->diff($todayTime);
        $timeLeft = $dayDiff->i.":".$dayDiff->s;
        if($timeLeft == '0:0'){
            $timeLeft = 'Finished';
        }

        if($todayTime->getTimestamp() > $matchDate->getTimestamp()){
            $match->endMatch($matchId);

            return new JsonResponse(array(
                'status' => 'Success',
                'message' => 'Match Is Over Id: '.$matchId),
                200);
        }

        $match->matchRandomEvent($matchId);

        $data[$matchData->getId()] = [
            'homeTeamScore' => $matchData->getHomeTeamScore(),
            'awayTeamScore' => $matchData->getAwayTeamScore(),
            'status' => $matchData->getStatus(),
            'timeLeft' => $timeLeft,
        ];

        return new JsonResponse(array(
            'status' => 'Success',
            'message' => 'Match Found For Id: '.$matchId,
            'data' => $data),
            200);
    }


    #[Route('/matches/activeMatches', name: 'activeMatches')]
    public function activeMatchesAction(ManagerRegistry $doctrine, Request $request): Response
    {

        $data = [];
        $activeMatches = $doctrine->getRepository(Matches::class)->findAllActiveMatches();

        if (empty($activeMatches)) {
            return new JsonResponse(array(
                'status' => 'None',
                'message' => 'No Active Matches',
                'count' => 0),
                200);
        }

        foreach ($activeMatches as $activeMatch){

            $match = $doctrine->getRepository(Matches::class);

            $todayTime = new \DateTime();
            $matchDate = new \DateTime($activeMatch->getMatchDate()->format('Y-m-d H:i:s'));
            $matchDate->modify("+10 Minutes");
            $dayDiff = $matchDate->diff($todayTime);
            $timeLeft = $dayDiff->i.":".$dayDiff->s;
            if($todayTime->getTimestamp() > $matchDate->getTimestamp()){
                $timeLeft = "Finished";
            }

            if($todayTime->getTimestamp() > $matchDate->getTimestamp()){
                $match->endMatch($activeMatch->getId());
            }

            $match->matchRandomEvent($activeMatch->getId());

            $data[$activeMatch->getId()] = [
                'homeTeamScore' => $activeMatch->getHomeTeamScore(),
                'awayTeamScore' => $activeMatch->getAwayTeamScore(),
                'status' => $activeMatch->getStatus(),
                'timeLeft' => $timeLeft,
            ];
        }

        return new JsonResponse(array(
            'status' => 'Success',
            'message' => 'All Matches',
            'data' => $data,
            'count' => count($data)),
            200);

    }



    #[Route('/matches/actives', name:'actives')]
    public function actives(ManagerRegistry $doctrine): Response
    {

        $matchRepo = $doctrine->getRepository(Matches::class);
        $activeMatches = $matchRepo->findAllActiveMatches();

        $data = [];
        foreach ($activeMatches as $activeMatch){
            $homeTeam = $doctrine->getRepository(Teams::class)->find($activeMatch->getHomeTeamId());
            $awayTeam = $doctrine->getRepository(Teams::class)->find($activeMatch->getAwayTeamId());
            $data[$activeMatch->getId()] = [
                'matchData' => $activeMatch,
                'homeData' => $homeTeam,
                'awayData' => $awayTeam,
                ];
        }

        return $this->render('matches/actives.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/matches/detail/{id}', name:'detail')]
    public function detail(ManagerRegistry $doctrine,int $id): Response
    {


        $matchRepo = $doctrine->getRepository(Matches::class)->find($id);
        if(empty($matchRepo)){ return $this->render('404.html.twig'); }

        $homeTeam = $doctrine->getRepository(Teams::class)->find($matchRepo->getHomeTeamId());
        $awayTeam = $doctrine->getRepository(Teams::class)->find($matchRepo->getAwayTeamId());
        $eventsRepo =  $doctrine->getRepository(MatchEvents::class)->findBy(
            ['match_id' => $id],
            ['id' => 'DESC']
        );


            $data = [
                'matchData' => $matchRepo,
                'eventData' => $eventsRepo,
                'homeData' => $homeTeam,
                'awayData' => $awayTeam,
            ];

        return $this->render('matches/detail.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/matches/finished')]
    public function finished(ManagerRegistry $doctrine): Response
    {

        $matchRepo = $doctrine->getRepository(Matches::class);
        $completedMatches = $matchRepo->findAllFinishedMatches();

        $data = [];
        foreach ($completedMatches as $completedMatch){
            $homeTeam = $doctrine->getRepository(Teams::class)->find($completedMatch->getHomeTeamId());
            $awayTeam = $doctrine->getRepository(Teams::class)->find($completedMatch->getAwayTeamId());
            $data[$completedMatch->getId()] = [
                'matchData' => $completedMatch,
                'homeData' => $homeTeam,
                'awayData' => $awayTeam,
            ];
        }

        return $this->render('matches/finished.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/matches/pending')]
    public function pending(ManagerRegistry $doctrine): Response
    {

        $matchRepo = $doctrine->getRepository(Matches::class);
        $pendingMatches = $matchRepo->findAllPendingMatches();

        $data = [];
        foreach ($pendingMatches as $pendingMatch){
            $homeTeam = $doctrine->getRepository(Teams::class)->find($pendingMatch->getHomeTeamId());
            $awayTeam = $doctrine->getRepository(Teams::class)->find($pendingMatch->getAwayTeamId());
            $data[$pendingMatch->getId()] = [
                'matchData' => $pendingMatch,
                'homeData' => $homeTeam,
                'awayData' => $awayTeam,
            ];
        }

        return $this->render('matches/pending.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/matches/start', name:'startMatch')]
    public function startAction(ManagerRegistry $doctrine, Request $request): Response
    {

        if(empty($request->get('matchId'))){
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'matchId is missing'),
                400);
        }

        $match = $doctrine->getRepository(Matches::class);
        if($match->startMatch($request->get('matchId'))){
            return new JsonResponse(array(
                'status' => 'Success',
                'message' => 'Match Started',
                'matchId' => $request->get('matchId')),
                200);
        }else{
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'Something went wrong',
                'matchId' => $request->get('matchId')),
                400);
        }
    }

    #[Route('/matches/new')]
    public function new(ManagerRegistry $doctrine){

        $teams = $doctrine->getRepository(Teams::class)->findAll();

        return $this->render('matches/new.html.twig', [
            'countries' => $teams,
        ]);
    }


    #[Route('/matches/create', name: 'create')]
    public function createAction(ManagerRegistry $doctrine, Request $request): Response
    {

        if(empty($request->get('homeTeamId'))){ throw $this->createNotFoundException('homeTeamId is missing'); }
        if(empty($request->get('awayTeamId'))){ throw $this->createNotFoundException('awayTeamId is missing'); }
        //if(empty($request->get('matchDate'))){ throw $this->createNotFoundException('matchDate is missing'); }

        $homeTeamId = $request->get('homeTeamId');
        $homeTeam = $doctrine->getRepository(Teams::class)->find($homeTeamId);
        if (!$homeTeam) {
            throw $this->createNotFoundException(
                'No team found for id '.$homeTeamId
            );
        }

        $awayTeamId = $request->get('awayTeamId');
        $awayTeam = $doctrine->getRepository(Teams::class)->find($awayTeamId);
        if (!$awayTeam) {
            throw $this->createNotFoundException(
                'No team found for id '.$awayTeamId
            );
        }

        /*$matchDate = $request->get('matchDate');
        if(\DateTime::createFromFormat('Y-m-d H:i:s',$matchDate) == false) {
            throw $this->createNotFoundException(
                'Match Date Is Wrong:  ' . $matchDate
            );
        }else{
            $matchDate = \DateTime::createFromFormat('Y-m-d H:i:s',$matchDate);
        }
        */

        $matchRepo = $doctrine->getRepository(Matches::class);
        $checkMatch = $matchRepo->checkMatchesExists($homeTeamId,$awayTeamId);

        if(!$checkMatch){
            $entityManager = $doctrine->getManager();
            $match = new Matches();
            $match->setHomeTeamId($homeTeamId);
            $match->setAwayTeamId($awayTeamId);
            $match->setStatus(self::NOT_STARTED);
            //$match->setMatchDate($matchDate);
            $match->setHomeTeamScore(0);
            $match->setAwayTeamScore(0);

            $entityManager->persist($match);

            $entityManager->flush();

            return new JsonResponse(array(
                'status' => 'Success',
                'message' => 'Saved new match with id '.$match->getId()),
                200);
        }
        else{
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'Match already exists'),
                200);
        }
    }
}

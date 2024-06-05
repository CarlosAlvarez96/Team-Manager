<?php

namespace App\Controller;

use App\Entity\ParticipantStats;
use App\Repository\ParticipantStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant; // Import the Participant class

#[Route('/participant/stats', name: 'participant_stats_')]
class ParticipantStatsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/all', name: 'all', methods: ['GET'])]
    public function getAllParticipantStats(ParticipantStatsRepository $participantStatsRepository): JsonResponse
    {
        $participantStats = $participantStatsRepository->findAll();
        $formattedStats = [];

        foreach ($participantStats as $stats) {
            $formattedStats[] = [
                'id' => $stats->getId(),
                'participant_id' => $stats->getParticipant()->getId(),
                'goals' => $stats->getGoals(),
                'assists' => $stats->getAssists(),
                'yellow_cards' => $stats->getYellowCards(),
                'red_cards' => $stats->getRedCards(),
            ];
        }

        return new JsonResponse($formattedStats);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createParticipantStats(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (
            !isset($data['participant_id']) ||
            !isset($data['goals']) ||
            !isset($data['assists']) ||
            !isset($data['yellow_cards']) ||
            !isset($data['red_cards'])
        ) {
            return new JsonResponse(['error' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $participantId = $data['participant_id'];
        $goals = $data['goals'];
        $assists = $data['assists'];
        $yellowCards = $data['yellow_cards'];
        $redCards = $data['red_cards'];

        $participantStats = new ParticipantStats();

        $participantStats->setParticipant($this->entityManager->getRepository(Participant::class)->find($participantId));
        $participantStats->setGoals($goals);
        $participantStats->setAssists($assists);
        $participantStats->setYellowCards($yellowCards);
        $participantStats->setRedCards($redCards);

        $this->entityManager->persist($participantStats);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Participant stats created successfully'], Response::HTTP_CREATED);
    }
}

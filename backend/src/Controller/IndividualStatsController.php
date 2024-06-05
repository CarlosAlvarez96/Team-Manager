<?php

namespace App\Controller;

use App\Entity\IndividualStats;
use App\Repository\IndividualStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;


#[Route('/individual-stats', name: 'app_individual_stats')]
class IndividualStatsController extends AbstractController
{
    #[Route('/all', name: 'individual_stats_all', methods: ['GET'])]
    public function getAllIndividualStats(IndividualStatsRepository $individualStatsRepository): JsonResponse
    {
        // Obtiene todos los registros de IndividualStats
        $individualStats = $individualStatsRepository->findAll();

        // Formatea los datos de los registros para la respuesta
        $formattedStats = [];
        foreach ($individualStats as $stats) {
            $formattedStats[] = [
                'id' => $stats->getId(),
                'pace' => $stats->getPace(),
                'shooting' => $stats->getShooting(),
                'physical' => $stats->getPhysical(),
                'defending' => $stats->getDefending(),
                'dribbling' => $stats->getDribbling(),
                'passing' => $stats->getPassing(),
                'position' => $stats->getPosition(),
                'user_id' => $stats->getUser() ? $stats->getUser()->getId() : null,
            ];
        }

        // Devuelve una respuesta JSON con todos los registros de IndividualStats
        return new JsonResponse($formattedStats);
    }
    #[Route('/create', name: 'create_individual_stats', methods: ['POST'])]
    public function createIndividualStats(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $stats = new IndividualStats();
        $stats->setPace($data['pace'] ?? null);
        $stats->setShooting($data['shooting'] ?? null);
        $stats->setPhysical($data['physical'] ?? null);
        $stats->setDefending($data['defending'] ?? null);
        $stats->setDribbling($data['dribbling'] ?? null);
        $stats->setPassing($data['passing']);
        $stats->setPosition($data['position'] ?? null);

        $user = $entityManager->getRepository(User::class)->find($data['user_id']);
        $stats->setUser($user);

        $entityManager->persist($stats);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Individual stats created successfully'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/user/{userId<\d+>}', name: 'individual_stats_by_user', methods: ['GET'])]
    public function getIndividualStatsByUserId(int $userId, IndividualStatsRepository $individualStatsRepository): JsonResponse
    {
        $stats = $individualStatsRepository->findOneBy(['user' => $userId]);

        if (!$stats) {
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $formattedStats = [
            'id' => $stats->getId(),
            'pace' => $stats->getPace(),
            'shooting' => $stats->getShooting(),
            'physical' => $stats->getPhysical(),
            'defending' => $stats->getDefending(),
            'dribbling' => $stats->getDribbling(),
            'passing' => $stats->getPassing(),
            'position' => $stats->getPosition(),
            'user_id' => $stats->getUser() ? $stats->getUser()->getId() : null,
        ];

        return new JsonResponse($formattedStats);
    }

    #[Route('/{id<\d+>}', name: 'individual_stats_get', methods: ['GET'])]
    public function getIndividualStatsById(int $id, IndividualStatsRepository $individualStatsRepository): JsonResponse
    {
        $stats = $individualStatsRepository->find($id);

        if (!$stats) {
            return new JsonResponse(['error' => 'IndividualStats not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $formattedStats = [
            'id' => $stats->getId(),
            'pace' => $stats->getPace(),
            'shooting' => $stats->getShooting(),
            'physical' => $stats->getPhysical(),
            'defending' => $stats->getDefending(),
            'dribbling' => $stats->getDribbling(),
            'passing' => $stats->getPassing(),
            'position' => $stats->getPosition(),
            'user_id' => $stats->getUser() ? $stats->getUser()->getId() : null,
        ];

        return new JsonResponse($formattedStats);
    }

    #[Route('/{id<\d+>}', name: 'individual_stats_update', methods: ['POST'])]
    public function updateIndividualStats(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $stats = $entityManager->getRepository(IndividualStats::class)->find($id);

        if (!$stats) {
            return new JsonResponse(['error' => 'IndividualStats not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (isset($data['pace'])) {
            $stats->setPace($data['pace']);
        }
        if (isset($data['shooting'])) {
            $stats->setShooting($data['shooting']);
        }
        if (isset($data['physical'])) {
            $stats->setPhysical($data['physical']);
        }
        if (isset($data['defending'])) {
            $stats->setDefending($data['defending']);
        }
        if (isset($data['dribbling'])) {
            $stats->setDribbling($data['dribbling']);
        }
        if (isset($data['passing'])) {
            $stats->setPassing($data['passing']);
        }
        if (isset($data['position'])) {
            $stats->setPosition($data['position']);
        }
        if (isset($data['user_id'])) {
            $user = $entityManager->getRepository(User::class)->find($data['user_id']);
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }
            $stats->setUser($user);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'IndividualStats updated successfully'], JsonResponse::HTTP_OK);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use App\Message\AlertMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class AlerteController extends AbstractController
{
    #[Route('/alerter', methods: ['POST'])]
    public function alerter(Request $request, Connection $connection, MessageBusInterface $bus): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $insee = $data['insee'] ?? null;

        if (!$insee) {
            return new JsonResponse(['error' => 'Code INSEE requis'], 400);
        }

        $destinataires = $connection->fetchAllAssociative('SELECT telephone FROM destinataires WHERE insee = ?', [$insee]);

        if (!$destinataires) {
            return new JsonResponse(['error' => 'Aucun destinataire trouvé'], 404);
        }

        foreach ($destinataires as $destinataire) {
            $bus->dispatch(new AlertMessage($destinataire['telephone'], "Alerte météo pour votre région !"));
        }

        return new JsonResponse(['message' => "Alertes en cours d'envoi"], 200);
    }
}

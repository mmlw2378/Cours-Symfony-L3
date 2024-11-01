<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Dette;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class DetteController extends AbstractController
{
    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    #[Route('/dette/{clientId}', name: 'app_client_dettes', requirements: ['clientId' => '\d+'])]
    public function index(int $clientId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($clientId);
        
        // Check if the client exists
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        // Build the query for fetching debts
        $debtsQuery = $entityManager->getRepository(Dette::class)
            ->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client);

        // Apply the status filter
        $status = $request->query->get('status');
        if ($status === 'solde') {
            $debtsQuery->andWhere('d.montant <= d.montantVerse');
        } elseif ($status === 'non_solde') {
            $debtsQuery->andWhere('d.montant > d.montantVerse');
        }

        // Paginate the results
        $dettes = $this->paginator->paginate(
            $debtsQuery->getQuery(),
            $request->query->getInt('page', 1),
            8 // Number of items per page
        );

        // Calculate the total amount of debts
        $totalAmount = array_reduce($dettes->getItems(), function ($sum, $dette) {
            return $sum + $dette->getMontant();
        }, 0);

        return $this->render('dette/index.html.twig', [
            'client' => $client,
            'dettes' => $dettes,
            'totalAmount' => $totalAmount,
            'currentPage' => $dettes->getCurrentPageNumber(),
            'totalPages' => ceil($dettes->getTotalItemCount() / 8),
            'status' => $status,
        ]);
    }
}
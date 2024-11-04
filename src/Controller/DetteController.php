<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Dette;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\DetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        $this->denyAccessUnlessGranted('VIEW', $client);

        $dettesQuery = $entityManager->getRepository(Dette::class)
            ->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client);

        $status = $request->query->get('status');
        if ($status === 'solde') {
            $dettesQuery->andWhere('d.montant <= d.montantVerse');
        } elseif ($status === 'non_solde') {
            $dettesQuery->andWhere('d.montant > d.montantVerse');
        }

        $dettes = $this->paginator->paginate(
            $dettesQuery->getQuery(),
            $request->query->getInt('page', 1),
            8
        );

        $totalAmount = array_reduce($dettes->getItems(), fn($sum, $dette) => $sum + $dette->getMontant(), 0);

        return $this->render('dette/index.html.twig', [
            'client' => $client,
            'dettes' => $dettes,
            'totalAmount' => $totalAmount,
            'currentPage' => $dettes->getCurrentPageNumber(),
            'totalPages' => ceil($dettes->getTotalItemCount() / 8),
            'status' => $status,
        ]);
    }

    #[Route('/dette/full-access', name: 'dette_full_access')]
    public function fullAccessAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUIER');
        return $this->render('dette/combined.html.twig');
    }

    #[Route('/dette/create', name: 'dette_create')]
    public function createDetteAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');
        // Add debt creation logic here if required
        return $this->render('dette/combined.html.twig');
    }

    #[Route('/dette/list', name: 'dette_list')]
    public function listDettesAction(Request $request, DetteRepository $detteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');

        $client = $request->query->get('client');
        $date = $request->query->get('date');
        $status = $request->query->get('status');

        $dettes = $detteRepository->findByFilters($client, $date, $status);

        return $this->render('dette/combined.html.twig', [
            'dettes' => $dettes,
        ]);
    }

    #[Route('/dette/{id}/details', name: 'dette_details')]
    public function viewDetteDetailsAction(Dette $dette): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');

        return $this->render('dette/combined.html.twig', [
            'dette' => $dette,
            'payments' => $dette->getPayments(),
        ]);
    }

    #[Route('/dette/{id}/register-payment', name: 'dette_register_payment')]
    public function registerPayment(Dette $dette, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');

        $payment = new Payment();
        $payment->setDette($dette);
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('dette_details', ['id' => $dette->getId()]);
        }

        return $this->render('payment/combined.html.twig', [
            'form' => $form->createView(),
            'dette' => $dette,
        ]);
    }
}

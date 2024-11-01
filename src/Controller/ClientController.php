<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/clients', name: 'app_client_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $clientsQuery = $entityManager->getRepository(Client::class)->createQueryBuilder('c');

        // Récupérer les filtres
        $surname = $request->query->get('surname');
        $telephone = $request->query->get('telephone');

        // Appliquer les filtres si nécessaire
        if ($surname) {
            $clientsQuery->andWhere('c.surname LIKE :surname')
                         ->setParameter('surname', '%' . $surname . '%');
        }
        if ($telephone) {
            $clientsQuery->andWhere('c.telephone LIKE :telephone')
                         ->setParameter('telephone', '%' . $telephone . '%');
        }

        // Pagination
        $page = $request->query->getInt('page', 1);
        $limit = 8;
        $offset = ($page - 1) * $limit;
        $clients = $clientsQuery->setFirstResult($offset)
                                ->setMaxResults($limit)
                                ->getQuery()
                                ->getResult();
        $totalClients = count($clientsQuery->getQuery()->getResult());
        $totalPages = ceil($totalClients / $limit);

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'surname' => $surname,
            'telephone' => $telephone,
        ]);
    }

    #[Route('/client/nouveau', name: 'app_client_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $surname = $request->request->get('surname');
            $telephone = $request->request->get('telephone');
            $adresse = $request->request->get('adresse');
            $login = $request->request->get('login');
            $password = $request->request->get('password');

            // Vérifier les doublons
            $existingClientSurname = $entityManager->getRepository(Client::class)->findOneBy(['surname' => $surname]);
            $existingClientTelephone = $entityManager->getRepository(Client::class)->findOneBy(['telephone' => $telephone]);
            $existingLogin = $entityManager->getRepository(User::class)->findOneBy(['username' => $login]);

            if ($existingClientSurname) {
                $errors['surname'] = 'Ce Surname existe déjà.';
            }
            if ($existingClientTelephone) {
                $errors['telephone'] = 'Ce Téléphone existe déjà.';
            }
            if ($existingLogin) {
                $errors['login'] = 'Ce nom d’utilisateur existe déjà.';
            }

            if (!$errors) {
                // Créer l'objet User et le persister
                $user = new User();
                $user->setLogin($login);
                $user->setPassword(password_hash($password, PASSWORD_BCRYPT)); // Hasher le mot de passe

                // Créer l'objet Client et le persister
                $client = new Client();
                $client->setSurname($surname);
                $client->setTelephone($telephone);
                $client->setAdresse($adresse);
                $client->setUser($user); // Associer l'utilisateur au client

                // Persister les deux entités
                $entityManager->persist($user);
                $entityManager->persist($client);
                $entityManager->flush();

                return $this->redirectToRoute('app_client_list');
            }
        }

        return $this->render('client/create.html.twig', [
            'errors' => $errors
        ]);
    }
}

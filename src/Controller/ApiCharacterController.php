<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use App\Form\ApiCharacterType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api-character')]
class ApiCharacterController extends AbstractController
{

    public function __construct(
        private HttpClientInterface $client
    ) {
    }
    #[Route('/', name: 'api_character_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/?size=10',
            [
                'auth_bearer' => $request->getSession()->get('token'), // Récupération du token
            ]
        );

        return $this->render('api-character/index.html.twig', [
            'characters' => $response->toArray(),
        ]);
    }

    #[Route('/new', name: 'api_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $character = new Character();
        $character = [];
        $form = $this->createForm(ApiCharacterType::class, $character);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['api_character'];
            unset($data['_token']);
            $response = $this->client->request(
                'POST',
                $this->getParameter('app.api_url') . '/characters/',
                [
                    'auth_bearer' => $request->getSession()->get('token'),
                    'json' => $data,
                ]
            );
            return $this->redirectToRoute('api_character_show', [
                'identifier' => $response->toArray()['identifier']
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->render('api-character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{identifier}', name: 'api_character_show', methods: ['GET'])]
    public function show(Request $request, string $identifier): Response
    {
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/' . $identifier,
            [
                'auth_bearer' => $request->getSession()->get('token'),
            ]
        );
        return $this->render('api-character/show.html.twig', [
            'character' => $response->toArray(),
        ]);
    }

    #[Route('/{identifier}/edit', name: 'api_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $identifier): Response
    {
        // Récupération du Character
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/' . $identifier,
            [
                'auth_bearer' => $request->getSession()->get('token'),
            ]
        );
        $character = $response->toArray();
        $form = $this->createForm(ApiCharacterType::class, $character);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['api_character']; // Récupération des données du formulaire
            unset($data['_token']); // Suppression du token
            $this->client->request(
                'PUT',
                $this->getParameter('app.api_url') . '/characters/' . $identifier,
                [
                    'auth_bearer' => $request->getSession()->get('token'),
                    'json' => $data,
                ]
            );
            return $this->redirectToRoute('api_character_show', [
                'identifier' => $identifier
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->render('api-character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{identifier}', name: 'api_character_delete', methods: ['POST'])]
    public function delete(Request $request, string $identifier): Response
    {
        if ($this->isCsrfTokenValid('delete' . $identifier, $request->request->get('_token'))) {
            $this->client->request(
                'DELETE',
                $this->getParameter('app.api_url') . '/characters/' . $identifier,
                [
                    'auth_bearer' => $request->getSession()->get('token')
                ]
            );
        }
        return $this->redirectToRoute('api_character_intelligence_number', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/characters/intelligence/{intelligence}', name: 'api_character_intelligence', methods: ['GET'])]
    public function getIntelligence(Request $request, int $intelligence): Response
    {

        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/intelligence',
            [
                'auth_bearer' => $request->getSession()->get('token'),
                'query' => [
                    'intelligence' => $intelligence,
                ]
            ]
        );

        return $this->render('api-character/index.html.twig', [
            'characters' => $response->toArray(),
        ]);
    }
}

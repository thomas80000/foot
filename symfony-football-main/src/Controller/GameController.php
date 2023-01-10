<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Form\GameType;
use App\Repository\GameRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    #[Route('/add_game', name: 'add_game')]
    public function add_game(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $game = new Game();

        dd($game);

        $form = $this->createForm(GameType::class, $game)
            ->add('Teams', EntityType::class, [
                'class' => Team::class,
                "choice_label" => 'name',
                "multiple" => true,
                'expanded' => true,
                'label' => "Choisissez les équipes"
            ])
            ->add('button', SubmitType::class, [
                'label' => "Créer le match"
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teams = $form->getData('teams')->getTeams();
            if (count($teams) != 2) {
                $this->addFlash('error', "Vous devez sélectionner 2 équipes");
            } else {
                foreach ($teams as $team) {
                    $team->addGame($game);
                }
                $entityManagerInterface->persist($game);
                $entityManagerInterface->flush();
            }
        }

        return $this->render('game/add_game.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();
        return $this->render('index.html.twig', [
            'games' => $games
        ]);
    }



    #[Route('/show_game/{id}', name: 'show_game')]
    public function show_game(Game $game, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $teams = $game->getTeams();

        $dateStart = $game->getDate();
        $currentDate = new DateTime('now', new DateTimeZone('Europe/Paris'));

        if ($dateStart < $currentDate) {
            $form = $this->createFormBuilder($game)
                ->add('winner', EntityType::class, [
                    'class' => Team::class,
                    'choice_label' => 'name',
                    'choices' => $teams,
                    'label' => "Choisissez le vainqueur: "
                ])
                ->add('button', SubmitType::class, [
                    'label' => "Ajouter le vainqueur",
                    'attr' => [
                        'class' => 'btn'
                    ]
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManagerInterface->persist($game);
                $entityManagerInterface->flush();
            }
            return $this->render('game/show_single_game.html.twig', [
                'game' => $game,
                'form' => $form->createView()
            ]);
        }
        return $this->render('game/show_single_game.html.twig', [
            'game' => $game
        ]);
    }
}

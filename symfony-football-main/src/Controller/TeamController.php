<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    #[Route('/add_team', name: 'add_team')]
    public function add_team(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $team = new Team();

        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($team);
            $entityManagerInterface->flush();
        }

        return $this->render('team/add_team.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

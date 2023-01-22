<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    #[Route('/livre/ajouter', name: 'app_livre_ajouter')]
    public function ajouterLivre(EntityManagerInterface $entityManager, Request $request): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setDateAjout(new \DateTime());
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('app_accueil');
        }
        return $this->render('livre/livre_ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/livre/voir/{id}', name: 'app_livre_voir')]
    public function voirLivre(EntityManagerInterface $entityManager, int $id): Response
    {
        //trouver le livre qui a le plus de vote
        $repo = $entityManager->getRepository(Livre::class);
        $query = $repo->createQueryBuilder('l')
            ->orderBy('l.votes', 'DESC')
            ->setMaxResults(1)
            ->getQuery();
        $livre_max = $query->getSingleResult();
        $vote_max =$livre_max->getVotes();

        $livre = $entityManager->getRepository(Livre::class)->find($id);
        //récupérer le genre
        $genre= $livre->getGenre();
        return $this->render('livre/voir.html.twig', [
            'livre' => $livre,
            'vote_max' => $vote_max,
            'genre' => $genre

        ]);
    }

    #[Route('/livre/voir/{id}/voter', name: 'app_livre_voter', methods:'post')]
    public function voterLivre(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $livre = $entityManager->getRepository(Livre::class)->find($id);
        //récupérer la valeur du vote
        $direction = $request->request->get('vote');

        if($direction === 'plus')
        {
            $livre->setVotes($livre->getVotes() + 1);
        } elseif($direction === 'moins')
        {
            $livre->setVotes($livre->getVotes() - 1);
        }
        $entityManager->flush();
        return $this->redirectToRoute('app_livre_voir', [
            'id' => $livre->getId(),
        ]);
    }
    #[Route('/livre/supprimer/{id}', name: 'app_livre_supprimer')]
    public function supprimerLivre(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $livre = $entityManager->getRepository(Livre::class)->find($id);
        $entityManager->getRepository(Livre::class)->remove($livre);
        $entityManager->flush();
        return $this->render('livre/succes.html.twig');
    }

}

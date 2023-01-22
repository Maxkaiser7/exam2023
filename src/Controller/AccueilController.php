<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //trouver le livre qui a le plus de vote
        $repo = $entityManager->getRepository(Livre::class);
        $query = $repo->createQueryBuilder('l')
            ->orderBy('l.votes', 'DESC')
            ->setMaxResults(1)
            ->getQuery();
        $livre_max = $query->getSingleResult();
        $vote_max =$livre_max->getVotes();

        $livres = $entityManager->getRepository(Livre::class)->findAll();
        return $this->render('base.html.twig', [
            'livres'=>$livres,
            'vote_max'=>$vote_max
        ]);
    }
}

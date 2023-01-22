<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Form\LivreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    #[Route('/genre', name: 'app_genre')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $genres = $entityManager->getRepository(Genre::class)->findAll();
        return $this->render('genre/genres.html.twig', [
            'genres' => $genres
        ]);
    }

    #[Route('/genre/voir/{id}', name: 'app_genre_voir')]
    public function voirArticlesParGenre(EntityManagerInterface $entityManager, int $id): Response
    {
        $genre = $entityManager->getRepository(Genre::class)->find($id);
        $livres = $genre->getLivres();
        return $this->render('genre/genre_voir.html.twig', [
            'genre' => $genre,
            'livres' => $livres
        ]);
    }

    #[Route('/genre/ajouter', name: 'app_genre_ajouter')]
    public function ajouterGenre(EntityManagerInterface $entityManager, Request $request): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('app_genre');
        }
        return $this->render('genre/genre_ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }


}

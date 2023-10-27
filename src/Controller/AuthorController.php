<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinMaxType;
use App\Repository\AuthorRepository;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/listauthor', name: 'app_listauthor')]
    public function listeauthor(AuthorRepository $rep): Response
    {
        $author=$rep->findAll28();
        return $this->render('author/list.html.twig', [
            'authors' => $author,
        ]);
    }
    #[Route('/MinMax', name: 'app_minmax')]
    public function MinMax(AuthorRepository $repo,Request $request): Response
    {
        $author=$repo->findAll();
        $form=$this->createForm(MinMaxType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $min=$form->get('min')->getData();
            $max=$form->get('max')->getData();
            //$author=$repo->findBy(['ref'=>$ref]);
            $author=$repo->findminmax($min,$max);
        }
        return $this->render('author/index.html.twig', [
            'author' => $author,'form'=>$form->createView()
        ]);
    }
    #[Route('/DeleteAuthorZeroBook', name: 'app_zerobook')]
    public function deleteAuthorsWithZeroBooks(AuthorRepository $authorRepository, EntityManagerInterface $entityManager)
    {
        $authorsWithZeroBooks = $authorRepository->findAuthorsWithZeroBooks();

        foreach ($authorsWithZeroBooks as $author) {
            $entityManager->remove($author);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_listauthor');
    }
    #[Route('/author/{authorId}/books', name: 'app_author_books')]
    public function booksByAuthor(BookRepository $bookRepository, AuthorRepository $authorRepository, int $authorId): Response
    {
        $author = $authorRepository->find($authorId);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $books = $bookRepository->findBy(['author' => $author]);

        return $this->render('book/listauthor.html.twig', [
            'books' => $books,
            'author' => $author,
        ]);
    }
    #[Route('/addauthor', name: 'app_addauthor')]
    public function addauthor(EntityManagerInterface $em ,Request $request): Response
    {
        $author=new Author();

        $form=$this->CreateForm(AuthorType::class,$author);
        $form->add("Add",SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("app_listauthor");
        }
        return $this->render('author/add.html.twig', [
            'form' =>$form->createView()
        ]);
    }
    #[Route('/updateauthor/{id}', name: 'app_updateauthor')]
    public function updateauthor(ManagerRegistry $doctrine ,Request $request,$id): Response
    {
        $repo=$doctrine->getRepository(Author::class);
        $author=$repo->find($id);


        $form=$this->CreateForm(AuthorType::class,$author);
        $form->add("Update",SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$doctrine->getManager();

            $em->flush();
            return $this->redirectToRoute("app_listauthor");
        }
        return $this->render('author/add.html.twig', [
            'form' =>$form->createView()
        ]);
    }
    #[Route('/deleteauthor/{id}', name: 'app_deleteauthor')]
    public function deleteauthor(ManagerRegistry $doctrine ,$id): Response
    {
        $repo=$doctrine->getRepository(Author::class);
        $author=$repo->find($id);




            $em=$doctrine->getManager();

            $em->remove($author);
            $em->flush();
            return $this->redirectToRoute("app_listauthor");

    }



}

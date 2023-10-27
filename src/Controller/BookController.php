<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Form\SearchRefType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/listbook', name: 'app_listbook')]
    public function showbook(BookRepository $repo): Response
    {
         // $books=$repo->findAll();
        //3. Afficher la liste des livres Trier par auteur
                 // $books = $repo->findAllByAuthor();
       //4. Afficher la liste des livres publiés avant l’année 2023 dont l’auteur a plus de 35
        //livres
                 //  $books=$repo->findAllByDateAndNbBook();
        $books = $repo->findBooksBetween2Dates();
        $nbpub=count($repo->findBy(['published'=>true]));
        $totalScienceFictionBooks = $repo->sumBooksInCategory();

        return $this->render('book/list.html.twig', [
            'books' => $books,'nbpub'=>$nbpub,'totalScienceFictionBooks'=>$totalScienceFictionBooks
        ]);
    }
    #[Route('/listbook/{id}', name: 'app_listbookbyauthor')]
    public function showbookbyauthor(BookRepository $repo,$id): Response
    {
        $books=$repo->showAllBooksByAuthor($id);


        return $this->render('book/bookbyauthor.html.twig', [
            'books' => $books
        ]);
    }
    #[Route('/searchref', name: 'app_searchref')]
    public function searchref(BookRepository $bookrepo,Request $request): Response
    {
        $books=$bookrepo->findAll();
        $form=$this->createForm(SearchRefType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $ref=$form->get('ref')->getData();
            //$books=$bookrepo->findBy(['ref'=>$ref]);
              $books=$bookrepo->findref($ref);
        }
        return $this->render('book/listandsearch.html.twig', [
            'books' => $books,'form'=>$form->createView()
        ]);
    }

     #[Route('/updatecategory', name: 'app_update_category')]
    public function updateCategoryBook(BookRepository $bookRepository)
    {
        $bookRepository->updateCategory();

        return $this->redirectToRoute('app_listbook');
    }
    #[Route('/author/{authorId}/books', name: 'app_author_books')]
    public function booksByAuthor(BookRepository $bookRepository, AuthorRepository $authorRepository, int $authorId): Response
    {
        $author = $authorRepository->find($authorId);

        $books = $bookRepository->findBy(['author' => $author]);

        return $this->render('book/listauthor.html.twig', [
            'books' => $books, 'author' => $author,
        ]);
    }

    #[Route('/addbook', name: 'app_addbook')]
    public function addbook(EntityManagerInterface $em,Request $request): Response
    {
       $book=new Book();
       $form=$this->CreateForm(BookType::class,$book);
       $book->setPublished(true);
        $form->add("Ajouter",SubmitType::class);

       $form->handleRequest($request);
       if($form->isSubmitted()&& $form->isValid()){
           $author=$book->getAuthor();
           if($author instanceof Author){
               $author->setNbBooks($author->getNbBooks()+1);
           }

           $em=$this->getDoctrine()->getManager();
           $em->persist($book);
           $em->flush();
           return $this->redirectToRoute("app_listbook");
       }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/updatebook/{ref}', name: 'app_updatebook')]
    public function updatebook(EntityManagerInterface $em,Request $request,$ref,BookRepository $repo): Response
    {
        $book=$repo->find($ref);
        $form=$this->CreateForm(BookType::class,$book);
        $form->add("Modifier",SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){

            $em=$this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute("app_listbook");
        }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/deletebook/{ref}', name: 'app_deletebook')]
    public function deletebook(BookRepository $repo,$ref,EntityManagerInterface $em): Response
    {
        $books=$repo->find($ref);
        $em->remove($books);
        $em->flush();
        return $this->redirectToRoute("app_listbook");
    }

    #[Route('/detailbook/{ref}', name: 'app_detailbook')]
    public function detailbook(BookRepository $repo,$ref,EntityManagerInterface $em): Response
    {
        $book=$repo->find($ref);

        return $this->render('book/detail.html.twig', [
            'detailbook' => $book,
        ]);
    }
}

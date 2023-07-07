<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    #[Route('/api/books/', name: 'app_total_book',methods:["GET"])]
    public function getAllBooks(BookRepository $bookRepository,SerializerInterface $serializer): JsonResponse
    {
        $Listbooks = $bookRepository->findAll();
        $jsonBook = $serializer->serialize($Listbooks,'json',["groups"=>"getBooks"]);

        return new JsonResponse(
            $jsonBook,Response::HTTP_OK,[],true
        );
        
    }

    #[Route('/api/books/{id}',name:'app_detail_book', methods:["GET"])]
    public function getDetailBooks(int $id,BookRepository $bookRepository,SerializerInterface $serializer):JsonResponse
    {
        $detailBooks = $bookRepository->find($id);


        if($detailBooks){
            $jsonBook = $serializer->serialize($detailBooks,'json',["groups"=>"getBooks"]);
            return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books/{id}',name:'app_delete_book', methods:["DELETE"])]
    public function deleteBooks(Book $book,EntityManagerInterface $em):JsonResponse
    {
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books',name:'app_create_book', methods:["POST"])]
    public function createBooks(Request $request,EntityManagerInterface $em,SerializerInterface $serializer,AuthorRepository $authorRepository,ValidatorInterface $validator):JsonResponse
    {
       $book = $serializer->deserialize($request->getContent(),Book::class, 'json');

       //on verifie les erreurs 
       $errors = $validator->validate($book);
        if($error)
       $content = $request->toArray();
       $idAuthor = $content['idAuthor'] ?? -1;
       $book->setAuthor($authorRepository->find($idAuthor));

       $em->persist($book);
       $em->flush();

       $jsonBook = $serializer->serialize($book,'json');
       //$location = $urlGenerator->generate('detailBook',['id'=>$book->getId()],UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook,Response::HTTP_CREATED,[],true);
    }

    
    #[Route('/api/books/{id}',name:'app_update_book', methods:["PUT"])]
    public function updateBooks(Request $request,EntityManagerInterface $em,SerializerInterface $serializer,Book $currentBook):JsonResponse
    {
       $updateBook = $serializer->deserialize($request->getContent(), Book::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE=>$currentBook]);
       
       $em->persist($updateBook);
       $em->flush();
       //$location = $urlGenerator->generate('detailBook',['id'=>$book->getId()],UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(null,Response::HTTP_NOT_FOUND,);
    }

}

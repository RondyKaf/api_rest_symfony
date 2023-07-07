<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class BookController extends AbstractController
{
    /*
    public function getAllBooks(Request $request,BookRepository $bookRepository,SerializerInterface $serializer,TagAwareCacheInterface $cahce): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $idCache = "getAllBooks" . $page . "-" . $limit;
        
        $jsonListBooks = $cahce->get($idCache,function(ItemInterface $item) use ($bookRepository,$page,$limit,$serializer){
            echo ("L'ELEMENT N'EST PAS ENCORE EN CACHE");
            $item->tag("booksCache");
            $listBooks =$bookRepository->findAllWithPagination($page,$limit);

            return $serializer->serialize($listBooks,'json',["groups"=>"getBooks"]);
        });
        

       
        return new JsonResponse(
            $jsonListBooks,Response::HTTP_OK,[],true
        );*/
    #[Route('/api/books/', name: 'app_total_book',methods:["GET"])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllBooks-" . $page . "-" . $limit;
        $bookList = $cache->get($idCache, function (ItemInterface $item) use ($bookRepository, $page, $limit) {
            echo ("N'EST PAS ENCORE ENREGISTRE");
            $item->tag("booksCache");
            return $bookRepository->findAllWithPagination($page, $limit);
        });

        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
   }
        
    
    @param Bo
    #[Route('/api/books/{id}',name:'app_detail_book', methods:["GET"])]
    public function getDetailBooks(int $id,BookRepository $bookRepository,SerializerInterface $serializer):JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getBooks"]);
        $detailBooks = $bookRepository->find($id);

        if($detailBooks){
            $jsonBook = $serializer->serialize($detailBooks,'json',);
            return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books/{id}',name:'app_delete_book', methods:["DELETE"])]
    public function deleteBooks(Book $book,EntityManagerInterface $em,TagAwareCacheInterface $cache):JsonResponse
    {   
        $cache->invalidateTags(["booksCache"]);
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books',name:'app_create_book', methods:["POST"])]
    #[IsGranted('ROLE_ADMIN', message:"vous n'avez pas le droit de créer un livre")]
    public function createBooks(Request $request,EntityManagerInterface $em,SerializerInterface $serializer,AuthorRepository $authorRepository,ValidatorInterface $validator):JsonResponse
    {
       $book = $serializer->deserialize($request->getContent(),Book::class, 'json');

       //on verifie les erreurs 
       $errors = $validator->validate($book);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),Response::HTTP_BAD_REQUEST,[],true);
        }
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


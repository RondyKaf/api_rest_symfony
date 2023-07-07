<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'app_all_authors',methods:["GET"])]
    public function getAllAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $jsonBook = $serializer->serialize($authorList,'json',["groups"=>"getBooks"]);

        return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
    }

    #[Route('/api/authors/{id}',name: 'app_detail_authors',methods:["GET"])]
    public function detailAuthors(int $id,AuthorRepository $authorRepository,SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->find($id);
        if($author){
            $jsonAuthor = $serializer->serialize($author,"json",["groups"=>"getBooks"]);
            return new JsonResponse($jsonAuthor,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_OK);
    }

    #[Route('/api/authors/{id}', name: 'deleteAuthor', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, EntityManagerInterface $em): JsonResponse {
        
        $em->remove($author);
        
        $em->flush();
        dd($author->getBooks());
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/authors', name:'app_create_author', methods:["POST"])]
    public function createAuthor(Request $request,EntityManagerInterface $em,SerializerInterface $serializer,UrlGeneratorInterface $urlGenerator):JsonResponse
    {
        $authors = $serializer->deserialize($request->getContent(),Author::class,'json');
        $em->persist($authors);
        $em->flush();

        $jsonAuthors = $serializer->serialize($authors,'json',["groups"=>"getBooks"]);
        return new JsonResponse()
    }
}

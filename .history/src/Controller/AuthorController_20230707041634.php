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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'app_all_authors',methods:["GET"])]
    public function getAllAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer,Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit',3);

        $idCache = 'getAllAuthors-' . $page . "-" . $limit;
        $listAuthors = $cache->get($idCache,function(ItemInterface $item) use ($authorRepository, $page,$limit){
            echo ("N'EST PAS ENCORE ENREGISTRE");
            $item->tag("authorsCache");

            return $authorRepository->findAllWithPagination($page,$limit);
        });
       
        $jsonBook = $serializer->serialize($listAuthors,'json',["groups"=>"getBooks"]);

        return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
    }

    #[Route('/api/authors/{id}',name: 'app_detail_authors',methods:["GET"])]
    public function detailAuthors(int $id,AuthorRepository $authorRepository,SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->find($id);
        if($author){
            $jsonAuthor = $serializer->serialize($author,"json",["groups"=>"getAuthors"]);
            return new JsonResponse($jsonAuthor,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_OK);
    }

    #[Route('/api/authors/{id}', name: 'deleteAuthor', methods: ['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un auteur')]
    public function deleteAuthor(Author $author, EntityManagerInterface $em, TagAwareCacheInterface $cache): JsonResponse {
        
        $em->remove($author);
        $em->flush();

        // On vide le cache.
        $cache->invalidateTags(["booksCache"]);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/authors', name:'app_create_author', methods:["POST"])]
    public function createAuthor(Request $request,EntityManagerInterface $em,SerializerInterface $serializer,ValidatorInterface $validate):JsonResponse
    {
        $authors = $serializer->deserialize($request->getContent(),Author::class,'json');

        $errors = $validate->validate($authors);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),Response::HTTP_BAD_REQUEST,[],true);
        }
        $em->persist($authors);
        $em->flush();

        $jsonAuthors = $serializer->serialize($authors,'json',["groups"=>"getBooks"]);
        return new JsonResponse($jsonAuthors,Response::HTTP_CREATED,[],true);
    }

    #[Route('/api/authors/{id}',name:"app_update_authors", methods:["PUT"])]
    public function updateAuthors(Request $request, SerializerInterface $serializer,
    EntityManagerInterface $em, Author $currentAuthors,ValidatorInterface $validate):JsonResponse{

        $errors = $validate->validate($currentAuthors);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        $updateAuthors = $serializer->deserialize($request->getContent(),Author::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthors]);
        $em->persist($updateAuthors);
        $em->flush();

        //on vide le cache
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }
}

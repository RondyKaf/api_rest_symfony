<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function getDetailAuthors(int $id,AuthorRepository $authorRepository,SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->find($id)
    }
}

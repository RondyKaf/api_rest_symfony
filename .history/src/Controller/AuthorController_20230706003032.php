<?php

namespace App\Controller;

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
        $booksList = $authorRepository->findAll();
        $jsonBook = $serializer->serialize($booksList,'json',["groups"=>"getBooks"]);

        return new JsonResponse($jsonBook,Response::HTTP_OK,[],true);
    }

    #[Route('/api/authors/{id}',name: 'app_detail_authors',methods:["GET"])]
    public function getDetailAuthors(Aut)
}

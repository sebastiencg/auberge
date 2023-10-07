<?php

namespace App\Controller;

use App\Repository\MotRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RoomRepository $roomRepository): Response
    {

        return $this->render('home/index.html.twig', [
            'rooms'=>$roomRepository->findAll()
        ]);
    }
}
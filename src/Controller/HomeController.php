<?php

namespace App\Controller;

use App\Repository\MotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MotRepository $motRepository): Response
    {
        $mot=$motRepository->findOneBy(["id"=>1]);
        return $this->render('home/index.html.twig', [
            'mot' => $mot,
        ]);
    }
}
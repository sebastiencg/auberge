<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/reservation/new/{id}', name: 'app_home_reservation', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Room $room, ReservationRepository $reservationRepository, ): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'room' => $room, // Pass the room to the form
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$this->getUser()){
                return  $this->redirectToRoute('app_register');
            }
            $dateDebut = Carbon::parse($reservation->getDateIn());
            $dateFin = Carbon::parse($reservation->getDateOut());
            $differenceJours = $dateFin->diffInDays($dateDebut);
            if ($differenceJours<1){
                $differenceJours=1;
            }

            $bedReservation=$reservation->getBed()->getReservations();
            foreach ($bedReservation as  $value){
                $messageErreur = "le lit (".$value->getBed()->getName().") est deja reserve a cette date";
                if ($reservation->getDateIn()>=$value->getDateIn()&&$reservation->getDateIn()<$value->getDateOut()){
                    return new Response($messageErreur, Response::HTTP_BAD_REQUEST);
                }
                if ($reservation->getDateOut()>=$value->getDateIn()&&$reservation->getDateOut()<=$value->getDateOut()){
                    return new Response($messageErreur, Response::HTTP_BAD_REQUEST);
                }
            }
            $reservation->setEmail($this->getUser());
            $reservation->setDay($differenceJours);
            $reservation->setPrice($room->getPrice()*$differenceJours);
            $reservation->setStatus(true);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[Route('/myReservation', name: 'app_reservation_home', methods: ['GET'])]
    public function reservation(ReservationRepository $reservationRepository): Response
    {
        if (!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        return $this->render('home/reservation.html.twig', [
            'reservations' => $reservationRepository->findBy(['Email'=>$this->getUser()]),
        ]);
    }

}
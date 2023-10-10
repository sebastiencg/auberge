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

#[Route('/admin/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/planning', name: 'app_reservation_planning', methods: ['GET'])]
    public function planning(ReservationRepository $reservationRepository): Response
    {
        $calendars=$reservationRepository->findAll();
        foreach ($calendars as $calendar){
            $data=[
                'id'=>$calendar->getId(),
                'start'=>$calendar->getDateIn()->format('Y-m-d H:i'),
                'end'=>$calendar->getDateOut()->format('Y-m-d H:i'),
                'title'=>$calendar->getName() .' '.$calendar->getBed()->getRoom()->getName().' '.$calendar->getBed()->getName().' montant $ '.$calendar->getPrice(),
                'statue'=>$calendar->getEmail(),
                'color'=>$calendar->getBed()->getRoom()->getColor()
            ];
            $datas[] = $data;
        }
        if (empty($datas)){
            $datas=[""];
        }

        $json=json_encode($datas);

        return $this->render('home/planning.html.twig',[
            'json'=>$json
        ]);
    }

    #[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
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
            /* savoir le nombre de place qui reste dans une room pendant une periode
            $bedOccupy=array();
            foreach ($room->getBed()->getValues() as $bed){
                foreach ($bed->getReservations()->getValues() as $item){
                    if ($reservation->getDateIn()>=$item->getDateIn()&&$reservation->getDateIn()<$item->getDateOut()){
                        $bedOccupy[] = $item;
                    }
                    else if ($reservation->getDateOut()>=$item->getDateIn()&&$reservation->getDateOut()<=$item->getDateOut()){
                        $bedOccupy[] = $item;
                    }
                }
            }
            dd(count($bedOccupy));*/

            $reservation->setEMail($this->getUser()->getEmail());
            $reservation->setDay($differenceJours);
            $reservation->setPrice($room->getPrice()*$differenceJours);
            $reservation->setStatus(true);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[Route('/check/Room/{id}', name: 'app_check_room', methods: ['GET'])]
    public function checkRoom(Room $room): Response
    {
        $date=new \DateTime();
        $bedOccupy=array();
        foreach ($room->getBed()->getValues() as $bed){
            foreach ($bed->getReservations()->getValues() as $item){
                if ($date>=$item->getDateIn()&&$date<$item->getDateOut()){
                    $bedOccupy[] = $item;
                }
            }
        }
        return $this->render('bed/checkBed.html.twig', [
            'beds' => $bedOccupy,
        ]);
    }
    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation=null, EntityManagerInterface $entityManager): Response
    {


        if(!$reservation){return $this->redirectToRoute('app_home');}
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
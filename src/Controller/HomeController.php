<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Hotel;
use App\Repository\ChambreRepository;
use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param ChambreRepository $chambreRepository
     * @param HotelRepository $hotelRepository
     * @return Response
     */
    public function index(Request $request, ChambreRepository $chambreRepository , HotelRepository $hotelRepository): Response
    {
        //duplication du code
        $form = $this->createFormBuilder()
            ->add('destination', TextType::class)
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('guests', ChoiceType::class, [
                'choices'  => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4+'=> 4,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rooms = $chambreRepository->findByInputs($data['destination'],$data['checkin'],$data['checkout'],$data['guests']);

            return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
                'rooms' => $rooms,
            ]);
        }

        $RecommendedHotels = $hotelRepository->findRecommendedHotels();

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(), 'RecommendedHotels' => $RecommendedHotels ,
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {

        return $this->render('home/contact.html.twig');
    }

    /**
     * @Route("/rooms", name="rooms")
     * @param Request $request
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function rooms(Request $request, ChambreRepository $chambreRepository): Response
    {
        //duplication du code
        $form = $this->createFormBuilder()
            ->add('destination', TextType::class)
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('guests', ChoiceType::class, [
                'choices'  => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4+'=> 4,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rooms = $chambreRepository->findByInputs($data['destination'],$data['checkin'],$data['checkout'],$data['guests']);
            return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
                'rooms' => $rooms,
                ]);
        }

        $rooms = $chambreRepository->findChambresByHotel('1');

        return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
            'rooms' => $rooms]
        );
    }

    /**
     * @Route("/rooms/{id}", name="roomDetails")
     */
    public function details(Chambre $chambre)
    {
        return $this->render('home/room.html.twig' , compact($chambre));
    }

    /**
     * @Route("/hotel/{hotelid}/rooms", name="hotelrooms")
     * @param int $hotelid
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function hotel(Request $request, int $hotelid, ChambreRepository $chambreRepository): Response
    {
        //duplication du code
        $form = $this->createFormBuilder()
            ->add('destination', TextType::class)
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('guests', ChoiceType::class, [
                'choices'  => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4+'=> 4,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rooms = $chambreRepository->findByInputs($data['destination'],$data['checkin'],$data['checkout'],$data['guests']);
            return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
                'rooms' => $rooms,
            ]);
        }

        $rooms = $chambreRepository->findChambresDisponibleByHotelForOneDay($hotelid);

        return $this->render('home/rooms.html.twig' , ['form' => $form->createView(),
            'rooms' => $rooms]);
    }

}

<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\ChambreRepository;
use App\Repository\HotelRepository;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        date_default_timezone_set("Africa/Casablanca");
        $this->session = $session;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param ChambreRepository $chambreRepository
     * @param HotelRepository $hotelRepository
     * @return Response
     */
    public function index(Request $request, ChambreRepository $chambreRepository, HotelRepository $hotelRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('destination', TextType::class)
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
                // 'data' => new \DateTime(),
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
                // 'data' => (new \DateTime())->add(new DateInterval('P1D')),
            ])
            ->add('guests', ChoiceType::class, [
                'choices' => [
                    'any' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4+' => 4,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        //filter form
        $filter = $this->createForm(FilterType::class);
        $filter->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $rooms = $chambreRepository->findByInputs($data['destination'], $data['checkin'], $data['checkout'], $data['guests']);
            $this->session->set('rooms', $rooms);
            $this->session->set('checkin', $data['checkin']);
            $this->session->set('checkout', $data['checkout']);

            return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
                'filter' => $filter->createView(),
                'rooms' => $rooms,
            ]);
        }

        $RecommendedHotels = $hotelRepository->findRecommendedHotels();
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(), 'RecommendedHotels' => $RecommendedHotels,
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
     * @Route("/hotel/{hotelid}/rooms", name="hotelroomsAvailable")
     * @param int $hotelid
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function hotelroomsAvailable(Request $request, int $hotelid, HotelRepository $hotelRepository, ChambreRepository $chambreRepository): Response
    {
        $hotelname = $hotelRepository->find($hotelid)->getNom();

        $form = $this->createFormBuilder()
            ->add('destination', TextType::class, [
                'data' => $hotelname,
            ])
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTime())->add(new DateInterval('P1D')),
            ])
            ->add('guests', ChoiceType::class, [
                'choices' => [
                    'any' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4+' => 4,
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        //filter form
        $filter = $this->createForm(FilterType::class);
        $filter->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rooms = $chambreRepository->findByInputs($data['destination'], $data['checkin'], $data['checkout'], $data['guests']);

            //Modifying session variables
            $this->session->set('rooms', $rooms);
            $this->session->set('checkin', $data['checkin']);
            $this->session->set('checkout', $data['checkout']);

            return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
                'filter' =>$filter->createView(),
                'rooms' => $rooms,
            ]);
        }

        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');
        $checkin = $today->format('Y-m-d H:i:s');
        $checkout = $tomorrow->format('Y-m-d H:i:s');

        $rooms = $chambreRepository->findByInputs($hotelname, $checkin, $checkout, 0);

        //Modifying session variables
        $this->session->set('rooms', $rooms);
        $this->session->set('checkin', $today);
        $this->session->set('checkout', $tomorrow);

        return $this->render('home/rooms.html.twig', ['form' => $form->createView(),
            'filter' =>$filter->createView(),
            'rooms' => $rooms,
        ]);
    }

    /**
     * @Route("/roomsfilter" , name="ajaxfilter")
     * @param Request $request
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function ajaxfilter(Request $request, ChambreRepository $chambreRepository) {

        //ajax call with page rendering
        if($request->isXmlHttpRequest()) {

            $data = $request->request->all();
            $params = $data['filter'];
            $rooms = $this->session->get('rooms');
            $checkin = $this->session->get('checkin')->format('Y-m-d');
            $checkout= $this->session->get('checkout')->format('Y-m-d');

            $filtredrooms = $chambreRepository->filter($rooms ,$params['stars'],$params['type'],$params['distance'],$params['maxprice']);
            return $this->render('home/roomsloader.html.twig', [
                'rooms' => $filtredrooms,
                'checkin' => $checkin,
                'checkout' => $checkout,
            ]);
        }

        return null;
    }

    /**
     * @Route("/roomsfiltercancel" , name="ajaxfiltercancel")
     * @param Request $request
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function filtercancel(Request $request, ChambreRepository $chambreRepository) {

        //ajax call with page rendering
        if($request->isXmlHttpRequest()) {

            $rooms = $this->session->get('rooms');
            $checkin = $this->session->get('checkin')->format('Y-m-d');
            $checkout= $this->session->get('checkout')->format('Y-m-d');

            return $this->render('home/roomsloader.html.twig', [
                'rooms' => $rooms,
                'checkin' => $checkin,
                'checkout' => $checkout,
            ]);
        }

        return null;
    }

    /**
     * @Route("/rooms/{id}/details/{checkin}/{checkout}", name="roomDetails")
     * @param Request $request
     * @param int $id
     * @param \DateTime $checkin
     * @param \DateTime $checkout
     * @param ChambreRepository $chambreRepository
     */
    public function details(Request $request, int $id, \DateTime $checkin, \DateTime $checkout, ChambreRepository $chambreRepository)
    {

        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('tele', TelType::class, [
                'required' => false,
            ])
            ->add('cinPass', TextType::class)
            ->add('checkin', DateType::class, [
                'data' => $checkin,
                'disabled' => true,
                'widget' => 'single_text',
            ])
            ->add('checkout', DateType::class, [
                'data' => $checkout,
                'disabled' => true,
                'widget' => 'single_text',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Reservation confirmation form + need mailing service.
        }

        $chambreValid = $chambreRepository->isChambreAvailable($id, $checkin, $checkout);

        if ($chambreValid == false || ($checkin > $checkout) || ($checkin == $checkout)) {
            return $this->redirectToRoute('home');
        } elseif ($chambreValid == true) {
            $chambre = $chambreRepository->find($id);
            return $this->render('home/room.html.twig', ['chambre' => $chambre,
                'form' => $form->createView(),
            ]);
        }

        //in case some troubleshooting happened
        return $this->redirectToRoute('home');
    }

}

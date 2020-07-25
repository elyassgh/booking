<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Form\FilterType;
use App\Repository\ChambreRepository;
use App\Repository\ClientRepository;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
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
     * @param ReservationRepository $reservationRepository
     * @param ClientRepository $clientRepository
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function details(Request $request, int $id, \DateTime $checkin, \DateTime $checkout, ChambreRepository $chambreRepository, ReservationRepository $reservationRepository, ClientRepository $clientRepository , \Swift_Mailer $mailer): Response
    {

        $chambreValid = $chambreRepository->isChambreAvailable($id, $checkin, $checkout);

        if ($chambreValid == false || ($checkin < (new \DateTime('today'))) || ($checkin > $checkout) || ($checkin == $checkout)  ) {
            //in case there is some link injections or some troubleshooting happened or the chambre is not available!
            return $this->redirectToRoute('home');
        }

        $chambre = $chambreRepository->find($id);

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

                $data = $form->getData();
                $now = new \DateTime('now');

                //handling phone number
                if (is_null($data['tele'])) $data['tele']="";

                $entityManager = $this->getDoctrine()->getManager();

                //client check
                $client = $clientRepository->findClientByCin($data['cinPass']);

                if (is_null($client)) {
                    //create new client
                    $client = new Client();
                    $client->setNom($data['name'])
                        ->setEmail($data['email'])
                        ->setTele($data['tele'])
                        ->setCinOrPassport($data['cinPass']);

                    //persist the reservation into the database
                    $entityManager->persist($client);
                    //executing database query
                    $entityManager->flush();

                    //getClientID for reference making purposes !
                    $clientID = $clientRepository->findLastInsertedClient()->getId();

                } else {
                    //update existing client (most importantly the email !)
                    $client->setNom($data['name'])
                        ->setEmail($data['email'])
                        ->setTele($data['tele']);

                    //executing database query
                    $entityManager->flush();

                    //getClientID for reference making purposes !
                    $clientID = $client->getId();

                }


                //Reference Generation Strategy : (incremented sequence) + "A" + (reservation date) + "L" + (reservation time) + "M" + (client id)
                $sequence  = $reservationRepository->generateSequence();
                $date = $now->format('Ymd');
                $time = $now->format('His');

                //total of the reservation
                $total = round( $chambre->getPrixSaison()->getPrix()*$chambre->getPrixSaison()->getTaux(), 2, PHP_ROUND_HALF_UP) *($checkout->diff($checkin)->d);

                //reference generation
                $reference = $sequence . "A" . $date . "L" . $time .  "M" . $clientID;

                //persist reservation
                $reservation = new Reservation();
                $reservation->setClient($client)
                    ->setChambre($chambre)
                    ->setReference($reference)
                    ->setDateReservation($now)
                    ->setCheckIn($checkin)
                    ->setCheckOut($checkout)
                    ->setTotal($total)
                ;
                //persist the reservation into the database
                $entityManager->persist($reservation);
                //executing database query
                $entityManager->flush();

                //email service

                $message = (new \Swift_Message('ALM BOOKING'))
                    ->setFrom('contact@alm.com')
                    ->setTo('elghazi.elyass@gmail.com')
                    ->setBody(
                        $this->renderView('email/confirmation.html.twig', [
                            'reservation' => $reservation,
                            'periode' => $checkout->diff($checkin)->d,
                        ]),
                        'text/html'
                    )
                ;

                //sending confirmation email
                $mailer->send($message);


                //rendering confirmation page
                return $this->render('home/confirmation.html.twig' , [
                    'email' => $data['email'] ,
                    'now' => $now ,
                    'checkin' =>$checkin ,
                    'checkout' =>$checkout,
                    'ref' => $reference,
                ]);

            }

        return $this->render('home/room.html.twig', ['chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }

}

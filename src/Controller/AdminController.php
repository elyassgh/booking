<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Images;
use App\Entity\PrixSaison;
use App\Entity\Service;
use App\Repository\AdminRepository;
use App\Repository\ChambreRepository;
use App\Repository\HotelRepository;
use App\Repository\ImagesRepository;
use App\Repository\ReservationRepository;
use App\Service\FileUploader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        date_default_timezone_set("Africa/Casablanca");
        $this->session = $session;
    }

    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {

        $admin = $this->getUser();
        return $this->render('admin/dashboard/index.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/credentials", name="credentials")
     */
    public function credentials(Request $request, HotelRepository $repository, FileUploader $fileUploader)
    {

        $admin = $this->getUser();

        $filesystem = new Filesystem();
        $entityManager = $this->getDoctrine()->getManager();
        //Imgs Directory
        $dir = $fileUploader->getTargetDirectory();

        $imgForm = $this->createFormBuilder()
            ->add('img', FileType::class)
            ->getForm();

        $hotel = $repository->find($admin->getHotel()->getId());
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, [
                'disabled' => true,
                'data' => $hotel->getNom(),
            ])
            ->add('region', ChoiceType::class, [
                'choices' => [
                    $hotel->getRegion() => $hotel->getRegion(),
                    'Tanger-Tetouan-Al Hoceima' => 'Tanger-Tetouan-Al Hoceima',
                    'Oriental' => 'Oriental',
                    'Fès-Meknès' => 'Fès-Meknès',
                    'Rabat-Salé-Kénitra' => 'Rabat-Salé-Kénitra',
                    'Béni Mellal-Khénifra' => 'Béni Mellal-Khénifra',
                    'Casablanca-Settat' => 'Casablanca-Settat',
                    'Marrakesh-Safi' => 'Marrakesh-Safi',
                    'Drâa-Tafilalet' => 'Drâa-Tafilalet',
                    'Souss-Massa' => 'Souss-Massa',
                    'Guelmim-Oued Noun' => 'Guelmim-Oued Noun',
                    'Laâyoune-Sakia El Hamra' => 'Laâyoune-Sakia El Hamra',
                    'Dakhla-Oued Ed-Dahab' => 'Dakhla-Oued Ed-Dahab',
                ]
            ])
            ->add('ville', TextType::class, [
                'data' => $hotel->getVille(),
            ])
            ->add('distance', TextType::class, [
                'data' => $hotel->getDistanceCentre(),
            ])
            ->add('adresse', TextType::class, [
                'data' => $hotel->getAdresse(),
            ])
            ->add('siteweb', TextType::class, [
                'data' => $hotel->getSiteweb(),
            ])
            ->add('stars', NumberType::class, [
                'data' => $hotel->getNbrEtoiles(),
                'attr' => [
                    'min' => 1,
                    'max' => 6,
                    'step' => 1
                ]
            ])
            ->add('description', TextareaType::class, [
                'data' => $hotel->getDescription(),
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $hotel->setRegion($data['region'])
                ->setVille($data['ville'])
                ->setDistanceCentre($data['distance'])
                ->setAdresse($data['adresse'])
                ->setSiteweb($data['siteweb'])
                ->setNbrEtoiles($data['stars'])
                ->setDescription($data['description']);
            $entityManager->flush();

            $form = $this->createFormBuilder()
                ->add('nom', TextType::class, [
                    'disabled' => true,
                    'data' => $hotel->getNom(),
                ])
                ->add('region', ChoiceType::class, [
                    'choices' => [
                        $hotel->getRegion() => $hotel->getRegion(),
                        'Tanger-Tetouan-Al Hoceima' => 'Tanger-Tetouan-Al Hoceima',
                        'Oriental' => 'Oriental',
                        'Fès-Meknès' => 'Fès-Meknès',
                        'Rabat-Salé-Kénitra' => 'Rabat-Salé-Kénitra',
                        'Béni Mellal-Khénifra' => 'Béni Mellal-Khénifra',
                        'Casablanca-Settat' => 'Casablanca-Settat',
                        'Marrakesh-Safi' => 'Marrakesh-Safi',
                        'Drâa-Tafilalet' => 'Drâa-Tafilalet',
                        'Souss-Massa' => 'Souss-Massa',
                        'Guelmim-Oued Noun' => 'Guelmim-Oued Noun',
                        'Laâyoune-Sakia El Hamra' => 'Laâyoune-Sakia El Hamra',
                        'Dakhla-Oued Ed-Dahab' => 'Dakhla-Oued Ed-Dahab',
                    ]
                ])
                ->add('ville', TextType::class, [
                    'data' => $hotel->getVille(),
                ])
                ->add('distance', TextType::class, [
                    'data' => $hotel->getDistanceCentre(),
                ])
                ->add('adresse', TextType::class, [
                    'data' => $hotel->getAdresse(),
                ])
                ->add('siteweb', TextType::class, [
                    'data' => $hotel->getSiteweb(),
                ])
                ->add('stars', NumberType::class, [
                    'data' => $hotel->getNbrEtoiles(),
                    'attr' => [
                        'min' => 1,
                        'max' => 6,
                        'step' => 1
                    ]
                ])
                ->add('description', TextareaType::class, [
                    'data' => $hotel->getDescription(),
                ])
                ->getForm();

            return $this->render('admin/credentials/index.html.twig', [
                'admin' => $admin,
                'form' => $form->createView(),
                'imgform' => $imgForm->createView(),
            ]);
        }

        $imgForm->handleRequest($request);
        if ($imgForm->isSubmitted() && $imgForm->isValid()) {
            $data = $imgForm->getData();
            $filesystem->remove(($dir . '/' . $hotel->getImage()));
            $imgname = $fileUploader->upload($data['img']);
            $hotel->setImage($imgname);
            $entityManager->flush();

            $form = $this->createFormBuilder()
                ->add('nom', TextType::class, [
                    'disabled' => true,
                    'data' => $hotel->getNom(),
                ])
                ->add('region', ChoiceType::class, [
                    'choices' => [
                        $hotel->getRegion() => $hotel->getRegion(),
                        'Tanger-Tetouan-Al Hoceima' => 'Tanger-Tetouan-Al Hoceima',
                        'Oriental' => 'Oriental',
                        'Fès-Meknès' => 'Fès-Meknès',
                        'Rabat-Salé-Kénitra' => 'Rabat-Salé-Kénitra',
                        'Béni Mellal-Khénifra' => 'Béni Mellal-Khénifra',
                        'Casablanca-Settat' => 'Casablanca-Settat',
                        'Marrakesh-Safi' => 'Marrakesh-Safi',
                        'Drâa-Tafilalet' => 'Drâa-Tafilalet',
                        'Souss-Massa' => 'Souss-Massa',
                        'Guelmim-Oued Noun' => 'Guelmim-Oued Noun',
                        'Laâyoune-Sakia El Hamra' => 'Laâyoune-Sakia El Hamra',
                        'Dakhla-Oued Ed-Dahab' => 'Dakhla-Oued Ed-Dahab',
                    ]
                ])
                ->add('ville', TextType::class, [
                    'data' => $hotel->getVille(),
                ])
                ->add('distance', TextType::class, [
                    'data' => $hotel->getDistanceCentre(),
                ])
                ->add('adresse', TextType::class, [
                    'data' => $hotel->getAdresse(),
                ])
                ->add('siteweb', TextType::class, [
                    'data' => $hotel->getSiteweb(),
                ])
                ->add('stars', NumberType::class, [
                    'data' => $hotel->getNbrEtoiles(),
                    'attr' => [
                        'min' => 1,
                        'max' => 6,
                        'step' => 1
                    ]
                ])
                ->add('description', TextareaType::class, [
                    'data' => $hotel->getDescription(),
                ])
                ->getForm();

            return $this->render('admin/credentials/index.html.twig', [
                'admin' => $admin,
                'form' => $form->createView(),
                'imgform' => $imgForm->createView(),
            ]);

        }

        return $this->render('admin/credentials/index.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
            'imgform' => $imgForm->createView(),
        ]);
    }


    /**
     * @Route("/reservations", name="reservations")
     */
    public function reservations(Request $request, ReservationRepository $repository)
    {

        $admin = $this->getUser();

        $reservations = $repository->findAllReservationToday($admin->getHotel()->getId());

        $form = $this->createFormBuilder()
            ->add('date', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('checkin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('id', TextType::class, [
                'required' => false,
                'attr' => [
                    'pattern' => '[A-Za-z0-9]{3,10}$',
                    'title' => "You can't use special characters or enter more than 10 characters",
                ]
            ])
            ->getForm();

        $form2 = $this->createFormBuilder()
            ->add('ref', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $reservations = $repository->findByInputs($admin->getHotel()->getId(), $data['date'], $data['checkin'], $data['id']);
            return $this->render('admin/reservations/index.html.twig', [
                'admin' => $admin,
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'reservations' => $reservations,
                'reservation' => null
            ]);
        }

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $data = $form2->getData();
            $reservation = $repository->findOneByReference($data['ref']);
        
            // don't show other hotels reservations to non hotel administrators
            if(($reservation->getChambre())->getHotel() != $admin->getHotel()) $reservation = null;

            return $this->render('admin/reservations/index.html.twig', [
                'admin' => $admin,
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'reservations' => $reservations,
                'reservation' => $reservation
            ]);
        }

        return $this->render('admin/reservations/index.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'reservations' => $reservations,
            'reservation' => null
        ]);
    }

    /**
     * @Route("/roomsmanagement", name="roomsmanagement")
     */
    public function roomsmanagement(Request $request, FileUploader $fileUploader,ChambreRepository $chambreRepository) {

        $admin = $this->getUser();

        $chambre = new Chambre();

        //create new room form
        $form = $this->createFormBuilder()
            ->add('num' , IntegerType::class , [
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ]
            ])
            ->add('etage', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Single room'=> 'Single room',
                    'King room' => 'King room',
                    'Luxury room' => 'Luxury room',
                    'Deluxe room' => 'Deluxe room',
                    'Twin room' => 'Twin room',
                    'Studio' => 'Studio'
                ]
            ])
            ->add('description' , TextareaType::class, [
                'attr' => [
                    'max_length' => 256
                ]
            ])
            ->add('superficie', NumberType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 0.01
                ]
            ])
            ->add('capacity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ]
            ])
            ->add('img', FileType::class)
            ->add('prix' , NumberType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 0.01
                ]
            ])
            ->add('taux' , NumberType::class, [
                'attr'=> [
                    'min' => 0.1,
                    'max' => 1,
                    'step' => 0.01
                ]
            ])
            ->add('services' , TextType::class , [
                'required' => false,
                'attr' => [
                    'data-role' => 'tagsinput',
                ]
            ])
            ->getForm()
        ;

        //select room form
        $selectform = $this->createFormBuilder()
            ->add('num' , EntityType::class , [
                'class' => Chambre::class,
                'query_builder' => function (ChambreRepository $repo) use ($admin) {
                    return $repo->createQueryBuilder('c')
                        ->andWhere('c.hotel = :hotel')
                        ->setParameter('hotel',$admin->getHotel())
                        ->orderBy('c.numero', 'ASC');
                },
                'choice_label' => 'numero',
            ])
            ->getForm()
        ;

        $form2 = $this->createFormBuilder()
            ->add('etage', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Single room' => 'Single room',
                    'King room' => 'King room',
                    'Luxury room' => 'Luxury room',
                    'Deluxe room' => 'Deluxe room',
                    'Twin room' => 'Twin room',
                    'Studio' => 'Studio'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'max_length' => 256
                ]
            ])
            ->add('superficie', NumberType::class)
            ->add('capacity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ],
            ])
            ->add('prix', NumberType::class, [
                'attr' => [
                    'min' => 1,
                    'step' => 0.01
                ],
            ])
            ->add('taux', NumberType::class, [
                'attr' => [
                    'min' => 0.1,
                    'max' => 1,
                    'step' => 0.01
                ],
            ])
            ->add('services', TextType::class, [
                'required' => false,
                'attr' => [
                    'data-role' => 'tagsinput',
                ],
            ])
            ->add('img', FileType::class, [
                'required' => false
            ])
            ->getForm()
        ;

        $selectform->handleRequest($request);
        if ($selectform->isSubmitted() && $selectform->isValid() ) {

            $data = $selectform->getData();
            $tmp = $data['num'];
            $this->session->set('tmp', $tmp);
            $services = $tmp->getServices()->toArray();
            $result = $chambreRepository->getServicesList($services);

            //edit a room form
            $form2 = $this->createFormBuilder()
                ->add('etage', IntegerType::class, [
                    'attr' => [
                        'min' => 1,
                        'step' => 1
                    ],
                    'data' => $tmp->getEtage()
                ])
                ->add('categorie', ChoiceType::class, [
                    'choices' => [
                        $tmp->getCategorie() => $tmp->getCategorie(),
                        'Single room' => 'Single room',
                        'King room' => 'King room',
                        'Luxury room' => 'Luxury room',
                        'Deluxe room' => 'Deluxe room',
                        'Twin room' => 'Twin room',
                        'Studio' => 'Studio'
                    ]
                ])
                ->add('description', TextareaType::class ,[
                    'attr' => [
                            'max_length' => 256
                    ],
                    'data' => $tmp->getDescription()
                ])
                ->add('superficie', NumberType::class, [
                    'attr' => [
                        'min' => 1,
                        'step' => 0.01
                    ],
                    'data' => $tmp->getSuperficie()
                ])
                ->add('capacity', IntegerType::class, [
                    'attr' => [
                        'min' => 1,
                        'step' => 1
                    ],
                    'data' => $tmp->getCapacity()
                ])
                ->add('prix', NumberType::class, [
                    'attr' => [
                        'min' => 1,
                        'step' => 0.01
                    ],
                    'data' => $tmp->getPrixSaison()->getPrix()
                ])
                ->add('taux', NumberType::class, [
                    'attr' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.01
                    ],
                    'data' => $tmp->getPrixSaison()->getTaux()
                ])
                ->add('services', TextType::class, [
                    'required' => false,
                    'attr' => [
                        'data-role' => 'tagsinput',
                    ],
                    'data' => $result
                ])
                ->add('img', FileType::class, [
                    'required' => false
                ])
                ->getForm()
            ;

            //RETURN !!!
            return $this->render('admin/rooms/index.html.twig' , [
                'admin' => $admin,
                'selectform' => $selectform->createView() ,
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'nbr' => $tmp->getNumero(),
                'img' => $tmp->getImage(),
                'id' => $tmp->getId(),
            ]);

        }

        $form2->handleRequest($request);

        if ($form2->isSubmitted() &&  $form2->isValid()) {
            $filesystem = new Filesystem();
            $data = $form2->getData();
            $room = $chambreRepository->find($this->session->get('tmp')->getId());
            //updating entity chambre attributes
            $room->setEtage($data['etage'])
                ->setCategorie($data['categorie'])
                ->setSuperficie($data['superficie'])
                ->setDescription($data['description'])
                ->setCapacity($data['capacity'])
            ;
            //updating chambre pricing attributes
            $room->getPrixSaison()->setPrix($data['prix'])
                ->setTaux($data['taux'])
            ;

            //Updating chambre image
            $img = $data['img'];
            if ($img) {
                $dir = $fileUploader->getTargetDirectory();
                if ($filesystem->exists((($dir.'/'.$room->getImage()))) && $room->getImage() != '') {
                    $filesystem->remove(($dir.'/'.$room->getImage()));
                }
                $imgname = $fileUploader->upload($img);
                $room->setImage($imgname);
            }

            //spliting services
            $roomServices = explode(',' ,$data['services']);

            //updating chambre services
            $room->getServices()->clear();
            //IMPORTANT !!!! --> refreshing the database
            $this->getDoctrine()->getManager()->flush();

            foreach ($roomServices as $servicename) {
                $service = new Service();
                $service->setLibelle($servicename);
                $room->addService($service);
                $this->getDoctrine()->getManager()->persist($service);
            }

            //update the database ;)
            $this->getDoctrine()->getManager()->flush();

            //RETURN !!!
            return $this->render('admin/rooms/index.html.twig' , [
                'admin' => $admin,
                'selectform' => $selectform->createView() ,
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'nbr' => $room->getNumero(),
                'img' => $room->getImage(),
                'id' => $room->getId(),
                'updated' => 'Room Updated Successfully.',
            ]);
        }


        //new room form handling
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $existed = $chambreRepository->findOneBy(['numero' => $data['num'] , 'hotel' => $admin->getHotel()]);
            if (is_null($existed)) {
                $chambre->setNumero($data['num']);
                $chambre->setEtage($data['etage']);
                $chambre->setCategorie($data['categorie']);
                $chambre->setSuperficie($data['superficie']);
                $chambre->setCapacity($data['capacity']);
                $chambre->setDescription($data['description']);
                $chambre->setHotel($admin->getHotel());

                $img = $data['img'];

                if ($img) {
                    $imgname = $fileUploader->upload($img);
                    $chambre->setImage($imgname);
                }
                $prixSaison = new PrixSaison();
                $prixSaison->setTaux($data['taux'])
                    ->setPrix($data['prix']);

                //relating the price to the room
                $chambre->setPrixSaison($prixSaison);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($chambre);

                $chambreServices = explode(',' ,$data['services']);
                foreach ($chambreServices as $servicename) {
                    $service = new Service();
                    $service->setLibelle($servicename);
                    $chambre->addService($service);
                    $this->getDoctrine()->getManager()->persist($service);
                }

                $entityManager->flush();

                //RETURN !!!
                return $this->render('admin/rooms/index.html.twig' , [
                    'admin' => $admin,
                    'form' => $form->createView(),
                    'selectform' => $selectform->createView() ,
                    'message' => 'Room Added Successfully.'
                ]);
            } else {
                //RETURN !!!
                return $this->render('admin/rooms/index.html.twig' , [
                    'admin' => $admin,
                    'form' => $form->createView(),
                    'selectform' => $selectform->createView() ,
                    'error' => 'It seems that you are already added this room.'
                ]);
            }
        }

        //RETURN !!!
        return $this->render('admin/rooms/index.html.twig' , [
            'admin' => $admin,
            'selectform' => $selectform->createView() ,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/roomsgallery", name="roomsgallery")
     */
    public function roomsgallery(Request $request, FileUploader $fileUploader, ChambreRepository $repo) {

        $admin = $this->getUser();

        //select room form
        $selectform = $this->createFormBuilder()
            ->add('num' , EntityType::class , [
                'class' => Chambre::class,
                'query_builder' => function (ChambreRepository $repo) use ($admin) {
                    return $repo->createQueryBuilder('c')
                        ->andWhere('c.hotel = :hotel')
                        ->setParameter('hotel',$admin->getHotel())
                        ->orderBy('c.numero', 'ASC');
                },
                'choice_label' => 'numero',
            ])
            ->getForm()
        ;

        $form = $this->createFormBuilder()
            ->add('Imgs' , FileType::class,[
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ]
            ])
            ->getForm()
        ;

        $selectform->handleRequest($request);

        if ($selectform->isSubmitted() && $selectform->isValid()) {
            $data = $selectform->getData();
            $this->session->set('chambre' , $data['num']);
            return $this->render('admin/gallery/index.html.twig' , [
                'admin' => $admin,
                'selectform' => $selectform->createView(),
                'chambre' => $data['num'],
                'form' => $form->createView()
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $chambre = $repo->find($this->session->get('chambre')->getId()) ;
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($data['Imgs'] as $img) {
                $image = new Images();
                $imgname = $fileUploader->upload($img);
                $image->setImage($imgname);
                $chambre->addImage($image);
                $this->getDoctrine()->getManager()->persist($image);
            }
            $entityManager->flush();
        }

        return $this->render('admin/gallery/index.html.twig' , [
            'admin' => $admin,
            'selectform' => $selectform->createView(),
        ]);
    }

    // delete an img by id
    /**
     * @Route("/img/delete/{id}", name="imagedelete")
     */
    public function deleteImg(int $id, ImagesRepository $repository, FileUploader $fileUploader)
    {
        $dir = $fileUploader->getTargetDirectory();
        $filesystem = new Filesystem();
        $image = $repository->find($id);
        //removing the file from the uploads directory
        $filesystem->remove(($dir.'/'.$image->getImage()));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('roomsgallery');
    }

    //delete a room
    /**
     * @Route("/delete/{id}", name="chambre_delete")
     */
    public function delete(Chambre $chambre, FileUploader $fileUploader): Response
    {
        $filesystem = new Filesystem();
        $dir = $fileUploader->getTargetDirectory();
        $filesystem->remove(($dir.'/'.$chambre->getImage()));
        $imgs = $chambre->getImages()->toArray();
        foreach ($imgs as $img) {
            $filesystem->remove(($dir.'/'.$img->getImage()));
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($chambre);
        $entityManager->flush();

        return $this->redirectToRoute('roomsmanagement');
    }


    // API Approach Methods --> JSON OBJECT RETURNED !! Used for dashboard statistics

    /**
     * @Route ("/yearIncome/{id}" , name="yearincome")
     */
    public function yearincome(int $id, ReservationRepository $repository) {
        return new JsonResponse($repository->yearIncome($id));
    }

    /**
     * @Route ("/monthRevenue/{id}" , name="monthRevenue")
     */
    public function monthRevenue(int $id, ReservationRepository $repository) {
        return new JsonResponse($repository->partialIncomeOfMonth($id));
    }

    /**
     * @Route ("/weekReservations/{id}" , name="weekbookings")
     */
    public function weekbooking(int $id, ReservationRepository $repository) {
        return new JsonResponse($repository->reservationOfTheWeek($id));
    }

    /**
     * @Route ("/trendingRooms/{id}" , name="trendingRooms")
     */
    public function trendingRooms(int $id, ChambreRepository $repository) {
        return new JsonResponse($repository->mostReservedRooms(($id)));
    }

}

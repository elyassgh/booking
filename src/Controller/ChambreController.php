<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Hotel;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
/**
 * @Route("/chambre")
 */
class ChambreController extends AbstractController
{
    /**
     * @Route("/", name="chambre_index", methods={"GET"})
     */
    public function index(ChambreRepository $chambreRepository): Response
    {
        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="chambre_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $chambre = new Chambre();

        $form = $this->createFormBuilder()
            ->add('num' , NumberType::class)
            ->add('etage', NumberType::class)
            ->add('categorie', TextType::class)
            ->add('superficie', NumberType::class)
            ->add('capacity', NumberType::class)
            ->add('hotel' , EntityType::class , [
                'class' => Hotel::class,
                'choice_label' => 'nom',

            ])
            ->add('img', FileType::class)
            ->getForm()
            ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $chambre->setNumero($data['num']);
            $chambre->setEtage($data['etage']);
            $chambre->setCategorie($data['categorie']);
            $chambre->setSuperficie($data['superficie']);
            $chambre->setCapacity($data['capacity']);
            $chambre->setHotel($data['hotel']);

            $img = $data['img'];
            if ($img) {
                $imgname = $fileUploader->upload($img);
                $chambre->setImage($imgname);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chambre);
            $entityManager->flush();

            return $this->redirectToRoute('chambre_index');
        }

        return $this->render('chambre/new.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chambre_show", methods={"GET"})
     */
    public function show(Chambre $chambre): Response
    {
        return $this->render('chambre/show.html.twig', [
            'chambre' => $chambre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chambre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Chambre $chambre): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('chambre_index');
        }

        return $this->render('chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chambre_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Chambre $chambre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chambre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chambre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chambre_index');
    }
}

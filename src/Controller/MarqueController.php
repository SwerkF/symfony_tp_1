<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'app_marque')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                
            $imageFile = $form->get('logo')->getData();

            if($imageFile)
            {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $marque->setLogo($newFilename);
            }

            $em->persist($marque);
            $em->flush();

            $this->addFlash(
                'success',
                'Marque crée avec succes !'
            );
    
            return $this->redirectToRoute('app_marque');
        };

        $marques = $em->getRepository(Marque::class)->findAll();
        return $this->render('marque/index.html.twig', [
            'controller_name' => 'MarqueController',
            'addform' => $form,
            'marques' => $marques
        ]);
    }

    #[Route('/{id}', name: 'app_marque_id')]
    public function marque(Marque $marque = null, Request $request, EntityManagerInterface $em): Response
    {

        if($marque == null) 
        {
            $this->addFlash(
                'danger',
                'Marque non trouvée !'
             );
            return $this->redirectToRoute('app_marque');
        }
        
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('logo')->getData();

            if($imageFile)
            {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $marque->setLogo($newFilename);
            }
        
            $em->persist($marque);
            $em->flush();

            $this->addFlash(
                'success',
                'Marque mise à jour'
            );

            return $this->redirectToRoute('app_marque');
        }

        return $this->render('marque/show.html.twig', [
            'controller_name' => 'MarqueController',
            'marque' => $marque,
            'editform' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'app_marque_delete')]
    public function delete(Marque $marque = null, Request $request, EntityManagerInterface $em): Response
    {

        if($marque == null) 
        {
            $this->addFlash(
                'danger',
                'Marque non trouvée'
             );
            return $this->redirectToRoute('app_marque');
        }
        
        $em->remove($marque);
        $em->flush();

        $this->addFlash(
            'success',
            'Marque supprimée avec succes !'
        );

        return $this->redirectToRoute('app_marque');
    }
}

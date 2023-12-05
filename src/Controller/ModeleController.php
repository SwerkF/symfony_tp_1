<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modele')]
class ModeleController extends AbstractController
{
    #[Route('/', name: 'app_modele')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $modele = new Modele();
        $form = $this->createForm(ModeleType::class, $modele);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                
            $em->persist($modele);
            $em->flush();

            $this->addFlash(
                'success',
                'Modele crée avec succes !'
            );
    
            return $this->redirectToRoute('app_modele');
        };

        $modeles = $em->getRepository(Modele::class)->findAll();
        return $this->render('modele/index.html.twig', [
            'controller_name' => 'ModeleController',
            'addform' => $form,
            'modeles' => $modeles
        ]);
    }

    #[Route('/{id}', name: 'app_modele_id')]
    public function modele(Modele $modele = null, Request $request, EntityManagerInterface $em): Response
    {

        if($modele == null) 
        {
            $this->addFlash(
                'danger',
                'Modele non trouvée !'
             );
            return $this->redirectToRoute('app_modele');
        }
        
        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($modele);
            $em->flush();

            $this->addFlash(
                'success',
                'Modele mise à jour'
            );

            return $this->redirectToRoute('app_modele');
        }

        return $this->render('modele/show.html.twig', [
            'controller_name' => 'ModeleController',
            'modele' => $modele,
            'editform' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'app_modele_delete')]
    public function delete(Modele $modele = null, Request $request, EntityManagerInterface $em): Response
    {

        if($modele == null) 
        {
            $this->addFlash(
                'danger',
                'Modele non trouvée'
             );
            return $this->redirectToRoute('app_modele');
        }
        
        $em->remove($modele);
        $em->flush();

        $this->addFlash(
            'success',
            'Modele supprimée avec succes !'
        );

        return $this->redirectToRoute('app_modele');
    }
}

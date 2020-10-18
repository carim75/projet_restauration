<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\Livraison;
use App\Entity\Societe;
use App\Form\SocieteType;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    /**
     * @Route("/accueil")
     */
    public function index()
    {
        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findBy(
            [
                'date'=> new \DateTime()
            ],
            [
                'id'=> 'ASC'
            ]

        );

        $repo = $this->getDoctrine()->getRepository(Commande::class);
        $commandes = $repo->findBy(
            [
                'date'=> new \DateTime()
            ],
            [
                'id'=> 'ASC'
            ]

        );



        return $this->render('index/index.html.twig', [
            'livraisons'=>$livraisons,
            'commandes'=>$commandes
        ]);
    }



    /**
     * @Route("/validlivraison/{idcom}")
     */
    public function validlivraison($idcom, EntityManagerInterface $manager)
    {

        $repo = $this->getDoctrine()->getRepository(Commande::class);
        $commande = $repo->find($idcom);
        $commande->setCheckfourn('oui');
        $livraison=new Livraison();
        var_dump($_POST['date']);
        $livraison->setCheckfourn('non');
        $livraison->setChecked('non');
        $livraison->setDate(new \DateTime($_POST['date']) );
        $livraison->setCommande($commande);
        $manager->persist($livraison);
        $manager->flush();



        return $this->redirectToRoute('app_index_index',[

        ]);

    }




    /**
     * @Route("/creasociete")
     */
    public function creasociete(SocieteRepository $societeRepository, Request $request, EntityManagerInterface $manager)
    {
        $societe= new Societe();
        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($societe);
            $manager->flush();
            $this->addFlash('success', 'Societe créée avec succès');
            return $this->redirectToRoute('app_index_index');
        }

      return $this->render('creasociete.html.twig',[
          'form' => $form->createView(),

      ]);
    }






    /**
     * @Route("facturecrea/{id}")
     */
    public function facturecrea($id,Request $request,EntityManagerInterface $manager)
    {
        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraison = $rep->find($id);
        $fact=$livraison->getCommande()->getId();
        $reposi=$this->getDoctrine()->getRepository(Commande::class);
        $commande = $reposi->find($fact);
        $commande->setCheckfourn('oui');
        $livraison->setCheckfourn('oui');
        $livraison->setChecked('oui');

        $facture=new Facture();

      $facture->setLivraison($livraison);
      $facture->setTva('10%');
        $manager->persist($facture);
        $manager->flush();
        $this->addFlash('success', 'Livraison validée, facture créée avec succès');

        $repo = $this->getDoctrine()->getRepository(Facture::class);
        $factures = $repo->findby(array(),array('id'=> 'DESC'));



       return $this->redirectToRoute("app_index_factures",[
           'factures'=>$factures
       ]);
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function factures()
    {
        $repo = $this->getDoctrine()->getRepository(Facture::class);
        $factures = $repo->findby(array(),array('id'=> 'DESC'));
        return $this->render("facture.html.twig",[
            'factures'=>$factures
        ]);
    }



}

<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Societe;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RestaurateursController extends AbstractController
{
    /**
     * @Route("/restaurateurs", name="restaurateurs")
     */
    public function index()
    {
        return $this->render('restaurateurs/index.html.twig', [
            'controller_name' => 'RestaurateursController',
        ]);
    }

    /**
     * @Route ("/ajoutpanier/{id}")
     *
     */
    public function ajoutPanier($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->add($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

        ]);

    }

    /**
     * @Route ("/retirerpanier/{id}")
     *
     */
    public function retirerPanier($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->remove($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

        ]);

    }

    /**
     * @Route ("/supprimerpanier/{id}")
     *
     */
    public function supprimerPanier($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->delete($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

        ]);

    }

    /**
     * @Route ("/achat/{id}")
     */
    public function commande($id, SocieteRepository $societeRepository, PanierService $panierService, EntityManagerInterface $manager)
    {

        $panier = $panierService->getFullPanier();


        $commande = new Commande();


        $commande->setTotal($panierService->getTotal());
        $commande->setRestaurateur($this->getUser()->getSociete());
        foreach ($panier as $item) {

            $achat = new Achat();
            $fournisseur = $item['produit']->getSociete();
            $achat->setProduit($item['produit']);
            $achat->setQuantite($item['quantite']);
            $achat->setPrix($item['produit']->getPrix());
            $manager->persist($achat);
            $achat->setCommande($commande);
            $panierService->delete($item['produit']->getId());

        }

        $commande->setFournisseur($fournisseur);
        $commande->setDate(new \DateTime());


        $manager->persist($commande);
        $manager->flush();


        return $this->redirectToRoute('app_index_listecommande');
    }
    /**
     * @Route ("/livraisonrestaurateur/{id}")
     */
    public function livraisonRestaurateur($id)
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findAll();

        return $this->render('restaurateurs/livraisonrestaurateur.html.twig',[
            'societe'=>$societe,
            'livraisons'=>$livraisons
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Entity\Produit;
use App\Entity\Societe;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FournisseursController extends AbstractController
{
    /**
     * @Route("/fournisseur", name="fournisseur")
     */
    public function index()
    {
        return $this->render('fournisseur/index.html.twig', [
            'controller_name' => 'FournisseurController',
        ]);
    }

    /**
     *
     * @Route("/ajouterproduit/{idsoc}")
     *
     */
    public function ajouterProduit($idsoc, Produit $produit = null, Request $request, EntityManagerInterface $manager, ProduitRepository $produitRepository, SocieteRepository $societeRepository)
    {

        /**
         * fonction de création du formulaire d'ajout de produit pour le fournisseur.
         * reliant directement les produit à la société afin d'éviter tout mélange de société
         */

        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * set de la societe sur le produit créé
             */
            $produit->setSociete($societeRepository->find($idsoc));
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/creationproduit.html.twig', [
            'FormProduit' => $form->createView(),
            'idsoc' => $idsoc
        ]);

    }

    /**
     *
     * @Route("modifierproduit/{id}")
     *
     */
    public function modifProduit(EntityManagerInterface $manager, Request $request, Produit $produit)
    {

        /**
         * fonction permettant la modification du produit
         */


        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/creationproduit.html.twig', [
            'FormProduit' => $form->createView(),
        ]);

    }

    /**
     * @Route("/supprimerproduit/{id}")
     */
    public function supprimerProduit(Request $request, Produit $produit)
    {

        /**
         * fonction permettant la suppression du produit
         */

        $delete = $this->getDoctrine()->getManager();
        $delete->remove($produit);
        $delete->flush();
        $this->addFlash('success', 'Produit supprimé avec succés');
        return $this->redirectToRoute('app_fournisseurs_listeprodfourn');
    }



    /**
     *
     * @Route("/produitfournisseur/{id}")
     *
     */
    public function listeProdFourn($id, Request $request, PaginatorInterface $paginator)
    {

        /**
         * fonction d'affichage des produit du fournisseur connecté
         */

        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $prods = $rep->findAllOrderBy($id);

        $produits = $paginator->paginate($prods, $request->query->getInt('page', 1), 10);

        return $this->render('fournisseur/produitsfournisseur.html.twig', [
            'produits' => $produits

        ]);

    }

    /**
     * @Route("/commandefournisseur/{id}")
     */
    public function commandesFournisseur($id)
    {

        /**
         * fonction d'affichage du récap de commande du restaurateur avant validation et passage dans l'onglet
         * livraison avec date de livraison setté
         */

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);



        return $this->render('fournisseur/commandesfournisseur.html.twig', [

            'societe' => $societe,


        ]);

    }

    /**
     * @Route("/livraison/{id}")
     */
    public function livraison($id)
    {

        /**
         * fonction d'affichage des livraison pour le préparateur de commande et le livreur
         * affichage par jour
         */
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findBy(
            [
                'date'=> new \DateTime()
            ],
            [
                'id'=> 'ASC'
            ]

        );

        return $this->render('fournisseur/listelivraisonsfournisseur.html.twig',[
            'societe'=>$societe,
            'livraisons'=>$livraisons
        ]);

    }
    /**
     *
     * @Route("/promosfournisseur/{id}")
     *
     */
    public function promosFourn(Request $request, $id)
    {

        /**
         * fonction d'affichage des promotions du fournisseur connecté avec possibilité de modifier le produit
         * si la promotion s'avère terminée
         */

        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findAllOrderBy($id);

        $promotion = $request->query->all();
        $produitsEnPromo = $rep->findBy([
            'promotion' => $promotion
        ]);

        return $this->render('fournisseur/promofournisseur.html.twig', [
            'produits' => $produits,
            'promotion' => $produitsEnPromo
        ]);


    }
}

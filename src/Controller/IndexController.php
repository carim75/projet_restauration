<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Achat;
use App\Entity\Facture;
use App\Entity\Livraison;
use App\Entity\Produit;
use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\ProduitType;
use App\Form\SocieteType;
use App\Form\UtilisateurType;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use App\Repository\UtilisateurRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{
    /**
     * @Route("/index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }


    /**
     * @Route ("/accueil")
     */
    public function homepage()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     *
     * @Route("/listeprodfourn/{id}")
     *
     */
    public function listeProdFourn($id)
    {


        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findAllOrderBy($id);

        return $this->render('listeprodfourn.html.twig', [
            'produits' => $produits

        ]);


    }

    /**
     * @Route ("/listeproduit/{societeid}", defaults={"societeid": ""} )
     */
    public function listeproduit(ProduitRepository $produitRepository, SocieteRepository $societeRepository, EntityManagerInterface $manager, Request $request, $societeid, PanierService $panierService)
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societes = $rep->findAll();

        $nom = $request->query->all();
        dump($nom);

        dump($panierService->getFullPanier());

        $soc = '';
        $societeid = $rep->findBy([
            'nom' => $nom]);

        $so = '';
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $prods = $repo->findAllOrderBy($societeid);

        $reposi = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $reposi->findAll();

        $reposito = $this->getDoctrine()->getRepository(Produit::class);
        $ps = $reposito->findAllOrderBy($so);

        return $this->render('fournisseur/listeproduit.html.twig', [
            'items' => $panierService->getFullPanier(),
            'total' => $panierService->getTotal(),
            'produits' => $produits,
            'societes' => $societes,
            'nom' => $nom,
            'societeid' => $societeid,
            'soc' => $soc,
            'prods' => $prods,
            'so' => $so,
            'ps' => $ps
        ]);
    }


    /**
     * @Route ("/add/{id}")
     *
     */
    public function panieradd($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->add($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

        ]);


    }

    /**
     * @Route ("/remove/{id}")
     *
     */
    public function panierremove($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->remove($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

        ]);


    }

    /**
     * @Route ("/delete/{id}")
     *
     */
    public function panierdelete($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->delete($id);

        return $this->redirectToRoute('app_index_listeproduit', [
            'societe' => $nomSociete

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
     * @Route("/livraison/{id}")
     */
    public function livraison($id)
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findAll();

        return $this->render('index/livraisons.html.twig',[
            'societe'=>$societe,
            'livraisons'=>$livraisons
        ]);

    }

    /**
     * @Route ("/livresto/{id}")
     */
    public function livresto($id)
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findAll();

       return $this->render('index/livresto.html.twig',[
           'societe'=>$societe,
           'livraisons'=>$livraisons
       ]);
    }

    /**
     * @Route("/commandefourn/{id}")
     */
    public function commandesFourn($id)
    {

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $soc = '';

        $tot = '';


        return $this->render('index/commandesfourn.html.twig', [

            'societe' => $societe,

            'soc' => $soc,
            'tot' => $tot
        ]);

    }

    /**
     * @Route("/commande")
     */
    public function listeCommande()
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societes = $rep->findAll();

        $repo = $this->getDoctrine()->getRepository(Commande::class);
        $commandes = $repo->findAll();

        $repos = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $repos->findAll();

        $reposi = $this->getDoctrine()->getRepository(Achat::class);
        $achats = $reposi->findBy(array(), array('commande' => 'ASC'));

        $soc = '';
        $soci = '';

        $tot = '';


        return $this->render('index/commandes.html.twig', [

            'societes' => $societes,
            'commandes' => $commandes,
            'achats' => $achats,
            'produits' => $produits,
            'soc' => $soc,
            'tot' => $tot,
            'soci' => $soci
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
        $commande->setChecked('non');
        $commande->setCheckfourn('non');
        foreach ($panier as $item) {

            $achat = new Achat();
            $achat->setProduit($item['produit']);
            $achat->setQuantite($item['quantite']);
            $achat->setPrix($item['produit']->getPrix());
            $commande->setFournisseur( $item['produit']->getSociete());
            $manager->persist($achat);
            $achat->setCommande($commande);
            $panierService->delete($item['produit']->getId());

        }


        $commande->setDate(new \DateTime());


        $manager->persist($commande);
        $manager->flush();


        return $this->redirectToRoute('app_index_listecommande');
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
     *
     * @Route("/editproduit/{idsoc}")
     *
     */
    public function editProduit($idsoc, Produit $produit = null, Request $request, EntityManagerInterface $manager, ProduitRepository $produitRepository, SocieteRepository $societeRepository)
    {


        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setSociete($societeRepository->find($idsoc));
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/editproduit.html.twig', [
            'FormProduit' => $form->createView(),
            'idsoc' => $idsoc
        ]);

    }

    /**
     * @Route("/deleteproduit/{id}")
     */
    public function deleteProduit(Request $request, Produit $produit)
    {

        $delete = $this->getDoctrine()->getManager();
        $delete->remove($produit);
        $delete->flush();
        $this->addFlash('success', 'Produit supprimé avec succés');
        return $this->redirectToRoute('app_index_listeproduit');
    }

    /**
     *
     * @Route("redit/{id}", name="redit_produit")
     *
     */
    public function modifprod(EntityManagerInterface $manager, Request $request, Produit $produit)
    {


        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/editproduit.html.twig', [
            'FormProduit' => $form->createView(),
        ]);

    }


    /**
     * @Route ("/promos/{societeid}", defaults={"societeid": ""})
     */
    public function promos(ProduitRepository $produitRepository, SocieteRepository $societeRepository, EntityManagerInterface $manager, Request $request, $societeid)
    {
        $repos = $this->getDoctrine()->getRepository(Produit::class);
        $produitsEnPromo = $repos->findAll();

        $nom = $request->query->all();
        $repo = $this->getDoctrine()->getRepository(Societe::class);
        $soc = '';
        $societeid = $repo->findBy([
            'nom' => $nom]);

        return $this->render('index/promotions.html.twig', [

            'produits' => $produitsEnPromo,
            'societeid' => $societeid,
            'soc' => $soc,
            'nom' => $nom
        ]);

    }

    /**
     *
     * @Route("/listepromofourn/{id}")
     *
     */
    public function listePromoFourn(Request $request, $id)
    {


        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findAllOrderBy($id);

        $promotion = $request->query->all();
        $produitsEnPromo = $rep->findBy([
            'promotion' => $promotion
        ]);

        return $this->render('listepromofourn.html.twig', [
            'produits' => $produits,
            'promotion' => $produitsEnPromo
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
        $factures = $repo->findAll();


       return $this->redirectToRoute("app_index_factures",[
           'factures'=>$factures
       ]);
    }

    /**
     * @Route("/factures")
     */
    public function factures()
    {
        $repo = $this->getDoctrine()->getRepository(Facture::class);
        $factures = $repo->findAll();
        return $this->render("facture.html.twig",[
            'factures'=>$factures
        ]);
    }



}

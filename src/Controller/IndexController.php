<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Achat;
use App\Entity\Livraison;
use App\Entity\Produit;
use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\ProduitType;
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
     * @Route ("/listeproduit/{societeid}", defaults={"societeid": ""} )
     */
    public function listeProduit(ProduitRepository $produitRepository, SocieteRepository $societeRepository, EntityManagerInterface $manager, Request $request, $societeid, PanierService $panierService)
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

        return $this->render('index/listeproduit.html.twig', [
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
     * @Route("/validlivraison/{idcom}")
     */
    public function validlivraison($idcom, EntityManagerInterface $manager)
    {

        $repo = $this->getDoctrine()->getRepository(Commande::class);
        $commande = $repo->find($idcom);

        $livraison=new Livraison();
        var_dump($_POST['date']);

        $livraison->setDate(new \DateTime($_POST['date']) );
        $livraison->setCommande($commande);
        $manager->persist($livraison);
        $manager->flush();



        return $this->redirectToRoute('app_index_index',[

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


}

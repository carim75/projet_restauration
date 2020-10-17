<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Produit;
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
     * @Route ("/ajoutpanier/{id}")
     *
     */
    public function ajoutPanier($id, PanierService $panierService, Request $request, ProduitRepository $produitRepository)
    {

        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->add($id);

        return $this->redirectToRoute('app_restaurateurs_listeproduit', [
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

        return $this->redirectToRoute('app_restaurateurs_listeproduit', [
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

        return $this->redirectToRoute('app_restaurateurs_listeproduit', [
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


        return $this->redirectToRoute('app_index_index');
    }
    /**
     * @Route ("/livraisonrestaurateur/{id}")
     */
    public function livraisonRestaurateur($id)
    {
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

        return $this->render('restaurateurs/livraisonrestaurateur.html.twig',[
            'societe'=>$societe,
            'livraisons'=>$livraisons
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
     * @Route("facturemodif/{id}")
     */
    public function facturemodif ($id, Request $request, EntityManagerInterface $manager)
    {
        $rep = $this->getDoctrine()->getRepository(Achat::class);
        $achat = $rep->find($id);





        return $this->render("facturemodif.html.twig",[
            'achat'=>$achat
        ]);
    }


    /**
     * @Route("validmodif/{id}")
     */
    public function validmodif($id,EntityManagerInterface $manager, Request $request)
    {
        $rep = $this->getDoctrine()->getRepository(Achat::class);
        $achat = $rep->find($id);

        $request->request->get('quantite');
        if($request->getMethod() == 'POST')

        {
            $ach=$request->get('quantite');

            $achat->setQuantite($ach);

        }

        $manager->persist($achat);
        $manager->flush();

        return $this->redirectToRoute("app_restaurateurs_livraisonrestaurateur");



    }

    /**
     * @Route ("/listeproduit/{societeid}", defaults={"societeid": ""})
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

        return $this->render('restaurateurs/listeproduit.html.twig', [
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


        return $this->render('restaurateurs/commandes.html.twig', [

            'societes' => $societes,
            'commandes' => $commandes,
            'achats' => $achats,
            'produits' => $produits,
            'soc' => $soc,
            'tot' => $tot,
            'soci' => $soci
        ]);

    }


}

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
        /**
         * fonction utilisant le service panier mis en place dans la session
         * pour ajouter (quantité du même produit) des produits au panier avant validation de la commande
         */

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

        /**
         * fonction utilisant le service panier mis en place dans la session
         * pour retirer ( quantité du même produit )des produits au panier avant validation de la commande
         */

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

        /**
         * fonction utilisant le service panier mis en place dans la session
         * pour supprimer intégralement la ligne de produit déja ajoutée au panier avant validation de la commande
         */


        $produit = $produitRepository->find($id);
        $nomSociete = $produit->getSociete()->getNom();

        $panierService->delete($id);

        return $this->redirectToRoute('app_restaurateurs_listeproduit', [
            'societe' => $nomSociete

        ]);

    }

    /**
     * @Route ("/achat")
     */
    public function commande(SocieteRepository $societeRepository, PanierService $panierService, EntityManagerInterface $manager)
    {

        /**
         * fonction appelant le service panier afin de le transformer en commande,
         * ainsi chaques produits avec leur quantité enregistrés dans le panier correspondra à un achat.
         * le cumul de tout ces achats aura un seule et même id de commande et créera donc une commande reliée par l'id aux achats, eux mêmes reliés aux produits en bdd
         */

        $panier = $panierService->getFullPanier();


        $commande = new Commande();


        $commande->setTotal($panierService->getTotal());
        $commande->setRestaurateur($this->getUser()->getSociete());
        /**
         * on set les check pour restaurateur et fournisseurs à 'non' afin qu'ils
         * restent affiché dans la liste des commandes restaurateur et fournisseurs
         * jusqu'à temps d'être setter à 'oui' lors de la validation de la commande par le fournisseur.
         * ainsi le status livraison commencera
         */

        $commande->setChecked('non');
        $commande->setCheckfourn('non');
        foreach ($panier as $item) {

            $achat = new Achat();
            $achat->setProduit($item['produit']);
            $achat->setQuantite($item['quantite']);
            $achat->setPrix($item['produit']->getPrix());
            $commande->setFournisseur($item['produit']->getSociete());
            $manager->persist($achat);
            $achat->setCommande($commande);
            $panierService->delete($item['produit']->getId());

        }


        $commande->setDate(new \DateTime());


        $manager->persist($commande);
        $manager->flush();
        $this->addFlash('success', 'Commande validée');


        return $this->redirectToRoute('app_index_index');
    }

    /**
     * @Route ("/livraisonrestaurateur")
     */
    public function livraisonRestaurateur()
    {

        /**
         * fonction permettant de checker les livraisons du jour,
         * les modifier ou les valider
         */

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($this->getUser()->getSociete());

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findBy(
            [
                'date' => new \DateTime()
            ],
            [
                'id' => 'ASC'
            ]

        );

        return $this->render('restaurateurs/livraisonrestaurateur.html.twig', [
            'societe' => $societe,
            'livraisons' => $livraisons
        ]);
    }


    /**
     * @Route ("/promos/{societeid}", defaults={"societeid": ""}, name="promoresto")
     */
    public function promos(ProduitRepository $produitRepository, SocieteRepository $societeRepository, EntityManagerInterface $manager, Request $request, $societeid)
    {
        /**
         * affichage de tout les produits classés par les fournisseurs en promotion. triés par fournisseurs
         */

        $repos = $this->getDoctrine()->getRepository(Produit::class);
        $produitsEnPromo = $repos->findAll();

        $nom = $request->query->all();
        $repo = $this->getDoctrine()->getRepository(Societe::class);
        $soc = '';
        $societeid = $repo->findBy([
            'nom' => $nom]);

        return $this->render('restaurateurs/promotions.html.twig', [

            'produits' => $produitsEnPromo,
            'societeid' => $societeid,
            'soc' => $soc,
            'nom' => $nom
        ]);

    }


    /**
     * @Route("facturemodif/{id}")
     */
    public function facturemodif($id, Request $request, EntityManagerInterface $manager)
    {
        /**
         *recupère l'achat à modifier lors de la livraison car non conforme
         */

        $rep = $this->getDoctrine()->getRepository(Achat::class);
        $achat = $rep->find($id);


        return $this->render("index/facturemodif.html.twig", [
            'achat' => $achat
        ]);
    }


    /**
     * @Route("validmodif/{id}")
     */
    public function validmodif($id, EntityManagerInterface $manager, Request $request)
    {
        /**
         * cette fonction permet d'enregistrer la modification de l'achat lors de la livraison (problème de quantité ou de qualité du produit qui serait alors renvoyer et remet à jour le montant de la ligne de commande mais
         * aussi le montant total de la livraison afin de pouvoir valider directement cette livraison et la transmettre
         * à la facturation
         *
         */


        $rep = $this->getDoctrine()->getRepository(Achat::class);
        $achat = $rep->find($id);
        $societe = $this->getUser()->getSociete()->getId();
        $acha = $request->request->get('quantite');


        $achat->setQuantite($acha);
        $manager->persist($achat);
        $manager->flush();
        $commande = $achat->getCommande();
        $achats = $commande->getAchats();
        $tot = 0;
        foreach ($achats as $ach) {
            $tot = $tot + ($ach->getPrix() * $ach->getQuantite());
        }
        $commande->setTotal($tot);
        $manager->persist($commande);
        $manager->flush();
        $this->addFlash('success', 'Modification effectuée');


        return $this->redirectToRoute('app_restaurateurs_livraisonrestaurateur', [
            'id' => $societe
        ]);

    }

    /**
     * @Route ("/listeproduit/{societeid}", defaults={"societeid": ""})
     */
    public function listeProduit(ProduitRepository $produitRepository, SocieteRepository $societeRepository, EntityManagerInterface $manager, Request $request, $societeid, PanierService $panierService)
    {

        /**
         * fonction permettant d'afficher tout les produits par fournisseur et de les commander (les ajouter au panier) lors de l'ajout d'un produit au panier, la liste des produits du même fournisseur est la seule présente
         * dans l'affichage afin de faciliter la commande. Il est de même mis à disposition du restaurateur la selection directe du fournisseur chez lequel il souhaite commander
         */

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societes = $rep->findAll();

        $nom = $request->query->all();


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

        /**
         * fonction permettant d'afficher toutes les commandes du jour effectuées
         */

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societes = $rep->findAll();

        $repo = $this->getDoctrine()->getRepository(Commande::class);
        $commandes = $repo->findBy([
            'date' => new \DateTime()
        ]);

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

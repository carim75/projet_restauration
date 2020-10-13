<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Achat;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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
     *@Route("/listeprodfourn/{id}")
     *
     */
    public function listeProdFourn($id) {


        $rep=$this->getDoctrine()->getRepository(Produit::class);
        $produits=$rep->findAllOrderBy($id);

        return $this->render('listeprodfourn.html.twig',[
            'produits' => $produits

        ]);


    }

    /**
     * @Route ("/listeproduit/{societeid}", defaults={"societeid": ""} )
     */
    public function listeproduit(ProduitRepository $produitRepository,SocieteRepository $societeRepository, EntityManagerInterface $manager,Request $request,$societeid, PanierService $panierService)
    {
        $rep=$this->getDoctrine()->getRepository(Societe::class);
        $societes=$rep->findAll();

        $nom=$request->query->all();
        dump($nom);

        dump($panierService->getFullPanier());

        $soc='';
        $societeid=$rep->findBy([
            'nom'=> $nom]);

        $repo=$this->getDoctrine()->getRepository(Produit::class);
        $prods=$repo->findAllOrderBy($societeid);

        $reposi=$this->getDoctrine()->getRepository(Produit::class);
        $produits=$reposi->findAll();

        return $this->render('fournisseur/listeproduit.html.twig',[
            'items'=>$panierService->getFullPanier(),
            'total'=>$panierService->getTotal(),
            'produits'=>$produits,
            'societes'=>$societes,
            'nom'=>$nom,
            'societeid'=>$societeid,
            'soc'=>$soc,
            'prods'=>$prods
        ]);
    }


    /**
     * @Route ("/add/{id}")
     *
     */
    public function panieradd($id, PanierService $panierService,Request $request, ProduitRepository $produitRepository)
    {

        $produit=$produitRepository->find($id);
        $nomSociete=$produit->getSociete()->getNom();

        $panierService->add($id);

        return $this->redirectToRoute('app_index_listeproduit',[
            'societe'=>$nomSociete

        ]);


    }

    /**
     * @Route ("/remove/{id}")
     *
     */
    public function panierremove($id, PanierService $panierService,Request $request, ProduitRepository $produitRepository)
    {

        $produit=$produitRepository->find($id);
        $nomSociete=$produit->getSociete()->getNom();

        $panierService->remove($id);

        return $this->redirectToRoute('app_index_listeproduit',[
            'societe'=>$nomSociete

        ]);


    }

    /**
     * @Route ("/delete/{id}")
     *
     */
    public function panierdelete($id, PanierService $panierService,Request $request, ProduitRepository $produitRepository)
    {

        $produit=$produitRepository->find($id);
        $nomSociete=$produit->getSociete()->getNom();

        $panierService->delete($id);

        return $this->redirectToRoute('app_index_listeproduit',[
            'societe'=>$nomSociete

        ]);


    }




    /**
     * @Route("/livraison")
     */
    public function livraison()
    {
        return $this->render('index/livraisons.html.twig');
    }


    /**
     * @Route("/commande")
     */
    public function listeCommande()
    {
        $rep=$this->getDoctrine()->getRepository(Societe::class);
        $societes=$rep->findAll();

        $repo=$this->getDoctrine()->getRepository(Commande::class);
        $commandes=$repo->findAll();

        $repos=$this->getDoctrine()->getRepository(Produit::class);
        $produits=$repos->findAll();

        $reposi=$this->getDoctrine()->getRepository(Achat::class);
        $achats=$reposi->findBy(array(), array('commande'=>'ASC'));


        $soc='';



        return $this->render('index/commandes.html.twig',[

            'societes'=>$societes,
            'commandes'=>$commandes,
            'achats'=>$achats,
            'produits'=>$produits,
            'soc'=>$soc
        ]);

    }


    /**
     * @Route ("/achat")
     */
    public function commande(PanierService $panierService,EntityManagerInterface $manager)
    {

        $panier=$panierService->getFullPanier();




        $commande=new Commande();



       foreach ($panier as $item) {

            $achat = new Achat();
            $achat->setProduit( $item['produit'] );
            $achat->setQuantite($item['quantite']);
            $achat->setPrix($item['produit']->getPrix());
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
     *
     * @Route("/editproduit")
     * @Route("redit/{id}", name="redit_produit")
     */
    public function editProduit(Produit $produit=null,Request $request, EntityManagerInterface $manager,ProduitRepository $produitRepository)
    {

        if (!$produit) {
            $creation = true;
            $produit = new Produit();
        }


        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_index_listeproduit', [
                'id' => $produit->getId()
            ]);
        }

        return $this->render('fournisseur/editproduit.html.twig', [
            'FormProduit' => $form->createView(),
            'editMode' => $produit->getId() !== null
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
         * @Route ("/promos")
         */
        public function promos()
        {

            $repos=$this->getDoctrine()->getRepository(Produit::class);
            $produits=$repos->findAll();


            return $this->render('index/promotions.html.twig',[

                'produits'=>$produits,
            ]);

        }



}

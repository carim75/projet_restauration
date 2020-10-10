<?php


namespace App\Service\Panier;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    protected $session;
    protected $produitRepository;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $this->session=$session;
        $this->produitRepository=$produitRepository;
    }


    public function add(int $id)
    {

        $panier= $this->session->get('panier', []);
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id]=1;
        }
        $this->session->set('panier', $panier);
    }

    public function remove(int $id)
    {

        $panier= $this->session->get('panier', []);
        if(!empty($panier[$id] )&& $panier[$id]>1){
            $panier[$id]--;

        }else{
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function delete(int $id)
    {
        $panier=$this->session->get('panier', []);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);

    }


    public function getFullPanier() : array
    {
        $panier = $this->session->get('panier', []);

        $panierDetail=[];
        foreach ($panier as $id => $quantite){
            if ($quantite < 1){ $quantite = 0;}
            $panierDetail[]=[
                'produit'=>$this->produitRepository->find($id),
                'quantite'=>$quantite
            ];
        }
        return $panierDetail;
    }

    public function getEmptyPanier()
    {
        $panier = $this->session->get('panier', []);
        unset($panier);

        return "Commande ok!";

    }


    public function getTotal() : float
    {
        $total=0;

        foreach ($this->getFullPanier() as $item){

            $total += $item['produit']->getPrix() * $item['quantite'];
        }


        return $total;
    }
}




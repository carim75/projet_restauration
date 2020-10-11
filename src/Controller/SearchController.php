<?php

namespace App\Controller;

use App\Entity\Produit;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{


    /**
     * @Route("/searchrender")
     */
    public function search(ProduitRepository $repository, Request $request)
    {
        $produit = $repository->search($request->query->get('search'));
        var_dump($produit);
        // si la requête retourne un produit, l'utilisateur est renvoyé vers la page detail
        if (count($produit) == 1){

            return $this->render('details.html.twig',
                [
                    'produit' => $produit[0]
                ]
            );
        }
        // si la requête ne retourne pas de produit, l'utilisateur est renvoyé vers une page
        // lui indiquant que sa requête n'a pas retourné de résultats
        elseif (count($produit) == 0){
            return $this->render('message.html.twig',
                [

                    'produit' => $produit,

                ]
            );
        }


    }

    /**
     * @Route("/handle_search")
     * @return JsonResponse
     */
    public function autocomplete(Request $request)
    {


        $term = $request->query->get('query');

        $array = $this->getDoctrine()
            ->getManager()
            ->getRepository(Produit::class)
            // la méthode présente dans le repository produitt est utilisée ici en paramètre
            ->autocomplete($term);

        // le résultat est ensuite encodé au format json pour l'appel en ajax
        return new JsonResponse($array);
    }






}

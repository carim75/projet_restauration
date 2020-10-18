<?php


namespace App\Controller\admin;

use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\PatronType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/inscription")
     * @Route("/edit/{id}", name="utilisateur_edit")
     */
    public function inscription(Utilisateur $utilisateur = null, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {

        /*
         * formulaire d'inscription utilisateur restreint pour les patrons
         * qui relie directement l'utilisateur à la societe et à sa fonction (restaurateur ou fournisseur)
         *
         */

        if (!$utilisateur){
            $creation = true;
            $utilisateur = new Utilisateur();
        }


        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        /**
         * relie la societe à l'utilisateur
         */
        $repo = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $repo->find($this->getUser()->getSociete()->getId());
        dump($societe);

        /**
         * relie la fonction à l'utilisateur
         */

        $fonction=$this->getUser()->getFonction();
        dump($fonction);

        $form->handleRequest($request);
        if($form->isSubmitted()){

            /*
             *mise en place du hachage du mot de passe
             * lors de l'inscription de l'utilisateur
             *
             */

                $utilisateur->setSociete($societe);
                $utilisateur->setFonction($fonction);
                $utilisateur->setPassword($encoder->encodePassword(
                    $utilisateur,
                    $utilisateur->getPlainMdp()
                ));

                $manager->persist($utilisateur);
                $manager->flush();

                if (isset($creation)){
                    $this->addFlash('success', 'L\'utilisateur a bien été ajouté');
                }else{
                    $this->addFlash('update', 'L\'utilisateur a été mis à jour');
                }


            /**
             * renvoie à la page de connexion de l'utilisateurune fois celui ci ajouté
             */
                return $this->redirectToRoute('app_utilisateur_connexion');

        }

        return $this->render('admin/utilisateur/inscription.html.twig', [
            'form' => $form->createView(),
            'editMode'=> $utilisateur->getId() !== null
        ]);

    }

    /**
     * @Route("/patron")
     *
     */
    public function inscriptionPatron(Utilisateur $utilisateur = null, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {

        /*
         * formulaire d'inscription du patron avec tout les champs
         * présent à remplir par le webmaster car présence du choix de toutes les sociétés.
         * ensuite celui ci accèdera au formulaire d'inscription de nouveaux utilisateurs directement
         * reliés à sa fonction (restaurateur ou fournisseur) et à sa société
         *
         */


            $utilisateur = new Utilisateur();


        $form = $this->createForm(PatronType::class,$utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            if ($form->isValid()){

                $utilisateur->setPassword($encoder->encodePassword(
                    $utilisateur,
                    $utilisateur->getPlainMdp()
                ));

                $manager->persist($utilisateur);
                $manager->flush();


                    $this->addFlash('success', 'L\'utilisateur a bien été ajouté');


                return $this->redirectToRoute('app_utilisateur_connexion', [
                    'id' => $utilisateur->getId()
                ]);
            }
            else{
                $this->addFlash('error', 'Le formulaire contient une erreur');
            }

        }

        return $this->render('admin/utilisateur/inscriptionpatron.html.twig',[
            'form' => $form->createView()

        ]);

    }

    /**
     * @Route("/liste_utilisateurs")
     */
    public function listeUtilisateurs(Request $request)
    {
        /**
         * permet la gestion des utilisateurs de la société uniquement en role admin
         * afin de modifier les mots de passe si oubliés, supprimer ou ajouter un utilisateur
         * de sa société
         */

        $repo = $this->getDoctrine()->getRepository(Utilisateur::class);

        $utilisateurs = $repo->findAll();

        $utilisateurs = $repo->findBy(array(), array('pseudo' => 'ASC'));

        return $this->render('admin/utilisateur/listeUtilisateurs.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }



    /**
     * @Route("/delete/{id}")
     */
    public function delete(Request $request, Utilisateur $utilisateur)
    {

        /**
         * permet la suppression d'un utilisateur
         */
        $delete = $this->getDoctrine()->getManager();
        $delete->remove($utilisateur);
        $delete->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succés');
        return $this->redirectToRoute('app_admin_utilisateur_listeutilisateurs');
    }


}
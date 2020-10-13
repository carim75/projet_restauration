<?php


namespace App\Controller\admin;

use App\Entity\Utilisateur;
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
        if (!$utilisateur){
            $creation = true;
            $utilisateur = new Utilisateur();
        }


        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            if ($form->isValid()){

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

                return $this->redirectToRoute('app_index_index', [
                    'id' => $utilisateur->getId()
                ]);
            }
            else{
                $this->addFlash('error', 'Le formulaire contient une erreur');
            }

        }

        return $this->render('admin/utilisateur/inscription.html.twig', [
            'form' => $form->createView(),
            'editMode'=> $utilisateur->getId() !== null
        ]);

    }

    /**
     * @Route("/liste_utilisateurs")
     */
    public function listeUtilisateurs(Request $request)
    {
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

        $delete = $this->getDoctrine()->getManager();
        $delete->remove($utilisateur);
        $delete->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succés');
        return $this->redirectToRoute('app_admin_utilisateur_listeutilisateurs');
    }


}
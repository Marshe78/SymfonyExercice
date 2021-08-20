<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Entity\ContenuPanier;
use App\Form\ContenuPanierType;
use App\Repository\ContenuPanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/contenu/panier")
 */
class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/", name="contenu_panier_index", methods={"GET"})
     */
    public function index(ContenuPanierRepository $contenuPanier): Response
    {
       //Afficher la page contenu panier
        return $this->render('contenu_panier/index.html.twig', [
            'contenu_paniers' => $contenuPanier->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="contenu_panier_new", methods={"GET","POST"})
     */
    public function new(Request $request, Produit $produit): Response
    { 
     
        if ($produit->getId() !== null){
            
            $userConnecte = $this->getUser();

            //On cherche dans la class panier si un panier existe avec l'utilisateur
            $getPanier = $this->getDoctrine()
            ->getRepository(Panier::class)
            ->findBy(['Utilisateur' => $userConnecte]);
            
            //si le panier nexiste pas 
            if (!$getPanier) {
                //on incrémente un nouveau panier si il est inexistant
                $lepanier = new Panier();
            }else{
                // On remplis le panier avec l'article cliqué 
                $newContenu = new ContenuPanier();
                $newContenu->setQuantite(1);
                $newContenu->setPanier($getPanier[0]);
                $newContenu->setDate(new \DateTime());

                //On enregistre
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newContenu);
                $entityManager->flush();
            }
             
        //On retourne le panier
        return $this->renderForm('contenu_panier/new.html.twig', [
            'contenu_panier' => $getPanier,
        
        ]);
            
        }
    }

    /**
     * @Route("/{id}", name="contenu_panier_show", methods={"GET"})
     */
    public function show(ContenuPanier $contenuPanier): Response
    {
       //On récup l'user connecté
        $userConnecte = $this->getUser();


        $getPanier = $this->getDoctrine()
        ->getRepository(Panier::class)
        ->findBy(['Utilisateur' => $userConnecte]);
       // $idProduit = $getPanier[0]['id'];
      

        $products = $this->getDoctrine()
        ->getRepository(Panier::class)
        ->findBy(['utilisateur_id' => $idProduit]);


        return $this->render('contenu_panier/show.html.twig', [
            'contenu_panier' => $contenuPanier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contenu_panier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ContenuPanier $contenuPanier): Response
    {
        $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Edité');
            return $this->redirectToRoute('contenu_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contenu_panier/edit.html.twig', [
            'contenu_panier' => $contenuPanier,
            'form' => $form,
        ]);
    }

    /**   Supprimé le contenu
     * @Route("/{id}", name="contenu_panier_delete", methods={"POST"})
     */
    public function delete(Request $request, Panier $panier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panier);
            $entityManager->flush();
            $this->addFlash('suprimé','Ajouté');
        }

        return $this->redirectToRoute('contenu_panier_index', [], Response::HTTP_SEE_OTHER);
    }
}

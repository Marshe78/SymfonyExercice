<?php

namespace App\Controller;

use App\Form\ProduitType;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProduitRepository $produitRepository, Request $r): Response
    { 
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)->findAll();

        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($r);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', $t->trans('note.added'));
        }

        return $this->render('default/index.html.twig', [
            'produits' => $produits,
            'ajout_form' => $form->createView(),
        ]);
    }
}

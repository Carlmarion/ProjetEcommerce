<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Entity\Produit;
use App\Form\CommentFormType;
use App\Form\ProduitType;
use App\Form\RechercheType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ECommerceController extends AbstractController
{
    /**
     * @Route("/e/commerce", name="app_e_commerce")
     */
    public function index(ProduitRepository $repo, Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->get('recherche')->getData();
            $tabProduits = $repo->getProduitByNom($data);
        }
        else
        {
           $tabProduits = $repo->findAll(); 
        }

        return $this->render('e_commerce/index.html.twig', [
            'produits' => $tabProduits,
            'rechercheForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/e/commerce/profil", name="e_commerce_profil")
     */
    public function profil(CommentaireRepository $comm, ProduitRepository $produit)
    {
        
        $commentaires = $comm->getCommentByUser($this->getUser());
        $produitajouts = $produit->getProduitByUser($this->getUser());
        //dd($produitajouts);


        return $this->render("e_commerce/profil.html.twig", [
            'commentaires' => $commentaires,
            'produitajouts' => $produitajouts
        ]);
    }

     /**
     * @Route("e/commerce/show/{id}", name="e_commerce_show")
     */

    public function show(Produit $produitSeul, Request $request, EntityManagerInterface $manager)
    {
       $content = new Commentaire;
       
       $form = $this->createForm(CommentFormType::class, $content);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {
           $content->setCreatedAt(new \DateTime);
           $content->setProduit($produitSeul);
           $content->setAuteur($this->getUser());
           $manager->persist($content);
           $manager->flush();
           return $this->redirectToRoute('e_commerce_show', [
               'id' => $produitSeul->getId()
           ]);
       }
       return $this->render("e_commerce/show.html.twig", [
           'formContent' => $form->createView(),
           'produit' => $produitSeul

       ]);
    }

    /**
     * @Route("/e/commerce/new", name="e_commerce_create")
     * @Route("/e/commerce/edit/{id}", name="e_commerce_edit")
     */
    public function form(Request $request, EntityManagerInterface $manager, Produit $produit= null)
    {
        $user = $this->getUser();

        if(!$produit)
        {
            $produit = new Produit;
            $produit->setUpdatedAt(new \DateTime());
            $produit->setAuteur($this->getUser());
        }
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('e_commerce_show',[
                'id' => $produit->getId(),
            ]);
        }
        return $this->render("e_commerce/form.html.twig", [
            'formProduit' => $form->createView(),
            'editMode' => $produit->getId() !== NULL
        ]);
    }
   

 
}

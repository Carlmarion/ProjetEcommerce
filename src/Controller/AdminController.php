<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Entity\Commentaire;
use App\Form\CategorieType;
use App\Form\CommentFormType;
use App\Form\ProduitFormType;
use App\Form\CategorieFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            
        ]);
    }

    /**
     * @Route("/admin/produits", name="admin_produits")
     */
    public function adminProduits(ProduitRepository $repo, EntityManagerInterface $manager)
    {
        

        $colonnes = $manager->getClassMetadata(Produit::class)->getFieldNames();

        
        $produits = $repo->findAll();

        return $this->render('admin/admin_produits.html.twig', [
            'produits' => $produits,
            'colonnes' => $colonnes
        ]);
    }

    /**
     * @Route("/admin/produit/new", name="admin_new_produit")
     * @Route("/admin/{id}/edit-article", name="admin_edit_produit")
     */
    public function formProduit(Produit $produit = null, Request $request, EntityManagerInterface $manager)
    {
        //dump($produit);

        if(!$produit)
        {
            $produit = new Produit;
        }

        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$produit->getId())
            {
                $produit->setUpdatedAt(new \DateTime);
            }

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash('success', 'Les modifications ont bien ??t?? enregistr??es!');

            return $this->redirectToRoute('admin_produits');

        }
        
        return $this->render('admin/form_produit.html.twig', [
            'formProduit' => $form->createView(),
            'editMode' => $produit->getId() !== null
        ]);
    }

    /**
     * @Route("/admin/produit/delete/{id}", name="delete_produit")
     */
    public function deleteProduit(EntityManagerInterface $manager, Produit $produit)
    {
        $nom = $produit->getNom();
        $manager->remove($produit);

        $manager->flush();

        $this->addFlash('success',"Le produit'" . $nom . "' a bien ??t?? supprim?? ");
        return $this->redirectToRoute('admin_produits');
    }

 /**
     * @Route("/admin/categorie", name="admin_categorie")
     */
    public function adminCategorie( CategorieRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Categorie::class)->getFieldNames();

        $categories = $repo->findAll();

        return $this->render("admin/admin_categorie.html.twig", [
            'categories' => $categories,
            'colonnes' => $colonnes
        ]);
    }

    /**
     * @Route("/admin/categorie/new", name="admin_new_categorie")
     * @Route("/admin/categorie/edit/{id}", name="admin_edit_categorie")
     */
    public function formCategorie(EntityManagerInterface $manager, Categorie $categorie= null, Request $request )
    {
       if(!$categorie)
       {
           $categorie = new Categorie;
       } 

       $form = $this->createForm(CategorieFormType::class, $categorie);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {
           $manager->persist($categorie);
           $manager->flush();

           if($request->attributes->get('_route') == 'admin_new_produit')
           {
               $this->addFlash("success", "Votre categorie ?? bien ??t?? cr????e ");
           }
           else
           {
                $this->addFlash("success", "votre categorie'" . $categorie->getTitre() . "' a bien ??t?? modifi??e");
           }
           return $this->redirectToRoute("admin_categorie");
       }
       return $this->render("admin/form_categorie.html.twig", [
           'categories' => $form->createView(),
           'editMode' => $categorie->getId() !== NULL
       ]);
    }

    /**
     * @Route("/admin/categorie/delete/{id}", name="admin_delete_categorie")
     */
    public function deleteCategory(EntityManagerInterface $manager, Categorie $categorie)
    {   
        $nom = $categorie->getTitre();
        $manager->remove($categorie);
        
        $manager->flush();

        $this->addFlash('success',"La cat??gorie'" . $nom . "' a bien ??t?? supprim??e ! ");
        return $this->redirectToRoute('admin_categorie');

       
    }

     /**
     * @Route("/admin/comment", name="admin_comment")
     */
    public function adminComments(EntityManagerInterface $manager, CommentaireRepository $repo)
    {
        $colonnes = $manager->getClassMetadata(Commentaire::class)->getFieldNames();
        $comments = $repo->findAll();

        return $this->render('admin/admin_comment.html.twig', [
            'comments' => $comments,
            'colonnes' => $colonnes
        ]);
    }

    /**
     * @Route("/admin/comment/new", name="admin_new_comment")
     * @Route("/admin/comment/edit/{id}", name="admin_edit_comment")
     */
    public function formComment(EntityManagerInterface $manager, Commentaire $commentaire = null, Request $request )
    {
        if(!$commentaire)
        {
            $commentaire = new Commentaire;
            $commentaire->setCreatedAt(new \DateTime());
            
        }

        $form = $this->createForm(CommentFormType::class, $commentaire);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $manager->persist($commentaire);
            $manager->flush();
        
            if($request->attributes->get('_route') == 'admin_new_comment')
            {
                // si je me trouve sur la route d'ajout d'un article 
                $this->addFlash("success", "votre commentaire ?? bien ??t?? ajout??! ");
            }
            else
            {
                // sinon je me trouve dans la route d'??dition d'un article 
                //$this->addFlash("success", "votre commentaire ??crit par'" . $commentaire->getAuteur()->getUsername() . "'a bien ??t?? modifi??");
            }
            return $this->redirectToRoute('admin_comment');
        }
        return $this->render("admin/form_comment.html.twig", [
            'formComment' => $form->createView(),
            'editMode' => $commentaire->getId()
        ]);
    }

    /**
     * @Route("/admin/comment/delete/{id}", name="admin_delete_comment")
     */
    public function deleteComment(EntityManagerInterface $manager, Commentaire $commentaire)
    {   
        $id = $commentaire->getId();
        
        $manager->remove($commentaire);
        
        $manager->flush();

        $this->addFlash('success',"Le commentaire '" . $id . "' a bien ??t?? supprim??e ! ");
        return $this->redirectToRoute('admin_comment');

        
    }

}



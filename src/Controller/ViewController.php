<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\PersonneRepository;
use App\Entity\User;
use App\Entity\Personne;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;

class ViewController extends AbstractController
{
    // page index
    #[Route('/index', name: 'index')]
    public function index(): Response
    {

        $context = [];
        return $this->render('view/home/index.html.twig', $context);
    }


    // list of users 
    #[Route('/users', name: 'users')]
    public function users(UserRepository $repo): Response
    {
        $users = $repo->getAll();
        if (!$users) {
            throw $this->createNotFoundException(
                'pas de user '
            );
        }

        $context = ['users' => $users];
        return $this->render('view/user/users.html.twig', $context);
    }


    // list of personnes
    #[Route('/personnes', name: 'personnes')]
    public function personnes(PersonneRepository $repo): Response
    {
        $personnes = $repo->getAll();
        if (!$personnes) {
            throw $this->createNotFoundException(
                'pas de personne '
            );
        }

        $context = ['personnes' => $personnes];
        return $this->render('view/personne/personnes.html.twig', $context);
    }



    // user form
    #[Route("/form/user", name: 'form')]
    public function create(Request $request, ManagerRegistry $doctrine)
    {
        $manager = $doctrine->getManager();
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('first_name', TextType::class, [
                'attr'=>[
                    "placeholder"=>"votre nom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('last_name', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('users');
            }

        $context = ['formUser' => $form->createView()];
        
        return $this->render('view/user/form-user.html.twig', $context);
    }

    // personne form
    #[Route("/form/personne", name: 'form_personne')]
    public function createPersonne(Request $request, ManagerRegistry $doctrine)
    {
        $manager = $doctrine->getManager();
        $personne = new Personne();
        $form = $this->createFormBuilder($personne)
            ->add('genre', TextType::class, [
                'attr'=>[
                    "placeholder"=>"votre nom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('nom', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('naissance', DateType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('nationalite', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            -> getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($personne);
                $manager->flush();
                return $this->redirectToRoute('personnes');
            }

        $context = ['formPersonne' => $form->createView()];
        return $this->render('view/personne/form-personne.html.twig', $context);
    }


    // update user
    #[Route('/user/{id}', name: 'update')]
    public function update(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $user = new User();
        $user = $manager->getRepository(User::class)->find($id);

        // form for update
        $form = $this->createFormBuilder($user)
            ->add('first_name', TextType::class, [
                'attr'=>[
                    "placeholder"=>"votre nom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('last_name', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            -> getForm();
            
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->flush();
                return $this->redirectToRoute('users');
            }
        
        $context = ['updateUser' => $form->createView()];
        return $this->render('view/user/update-user.html.twig', $context);
    }

    // update personne
    #[Route('/personne/{id}', name: 'update_personne')]
    public function updatePersonne(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $personne = new Personne();
        $personne = $manager->getRepository(Personne::class)->find($id);

        // form for update
        $form = $this->createFormBuilder($personne)
            ->add('genre', TextType::class, [
                'attr'=>[
                    "placeholder"=>"votre nom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('nom', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('naissance', DateType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            ->add('nationalite', TextType::class, [
                'attr'=>[
                    'placeholder'=>"votre prenom",
                    // "class" => "form-control"
                    ]
                ])
            -> getForm();
            
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->flush();
                return $this->redirectToRoute('personnes');
            }
        
        $context = ['updatePersonne' => $form->createView()];
        return $this->render('view/personne/update-personne.html.twig', $context);
    }


    // Delete user
    #[Route('/delete/user/{id}', name: 'delete')]
    public function delete(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $user = $manager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'l\'utilisateur à l\'id '.$id. ' n\'existe pas'
            );
        }

        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('users');
    }

    // Delete personne
    #[Route('/delete/personne/{id}', name: 'delete_personne')]
    public function deletePersonne(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $personne = $manager->getRepository(Personne::class)->find($id);
        if (!$personne) {
            throw $this->createNotFoundException(
                'l\'utilisateur à l\'id '.$id. ' n\'existe pas'
            );
        }

        $manager->remove($personne);
        $manager->flush();
        return $this->redirectToRoute('personnes');
    }
}
<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;

#[Route('/animal')]
class AnimalController extends AbstractController
{
    #[Route('/', name: 'app_animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        return $this->render('animal/index.html.twig', [
            'animals' => $animalRepository->findAll(),
        ]);
        
    }

    #[Route('/new', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnimalRepository $animalRepository): Response
    {
        $animal = new Animal();
        $animal->setAbate(false);
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procura por um animal vivo com o mesmo código
            $existingAnimal = $animalRepository->findOneBy(['codigo' => $animal->getCodigo(), 'abate' => false]);
        
            if ($existingAnimal !== null && $existingAnimal !== true) {
                // Se um animal for encontrado, adiciona uma mensagem de erro ao formulário
                $form->get('codigo')->addError(new FormError('Já existe um animal vivo com esse código.'));
            } else {
                // Caso contrário, salva o registro normalmente
                $animalRepository->save($animal, true);
        
                $this->addFlash('success', 'O registro foi salvo com sucesso!');
        
                return $this->redirectToRoute('animal_relatorioAnimais', [], Response::HTTP_SEE_OTHER);
            }
        }
        
        



        

        return $this->renderForm('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_show', methods: ['GET'])]
    public function show(Animal $animal): Response
    {
        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_animal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animal $animal, AnimalRepository $animalRepository): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $animalRepository->save($animal, true);

            $this->addFlash('success', 'A alteração foi salva com sucesso!');

            return $this->redirectToRoute('animal_relatorioAnimais', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal, AnimalRepository $animalRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->request->get('_token'))) {
            $animalRepository->remove($animal, true);
        }

        $this->addFlash('success', 'Registro excluido com sucesso!');

        return $this->redirectToRoute('animal_relatorioAnimais', [], Response::HTTP_SEE_OTHER);
    }

}

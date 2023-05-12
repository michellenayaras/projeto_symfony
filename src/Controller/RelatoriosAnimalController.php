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


class RelatoriosAnimalController extends AbstractController
{
    #[Route('/', name: 'animal_relatorioAnimais', methods: ['GET'])]
    public function relatorioAnimais(AnimalRepository $animalRepository): Response
    {

        // $queryBuilder = $animalRepository->createQueryBuilder('p')
        //     ->orderBy('p.nascimento', 'ASC')
        //     ->getQuery();

        // $animaisPaginados = $paginator->paginate(
        //     $queryBuilder,
        //     $request->query->getInt('page', 1),
        //     10
        // );

        return $this->render('animal/relatorio_animais.html.twig', [
            'animals' => $animalRepository->findAll(),
            //'animals' => $animaisPaginados,
        ]);
    }

    #[Route('/animais_para_abate', name: 'animal_relatorioAnimaisAbate', methods: ['GET'])]
    public function relatorioAnimaisAbate(AnimalRepository $animalRepository): Response
    {
        $animaisAbate = $animalRepository->getAnimaisParaAbate();

        return $this->render('animal/relatorio_animais_abate.html.twig', [
            'animals' => $animaisAbate,
        ]);
    }

    #[Route('/animais_abatidos', name: 'animal_relatorioAnimaisAbatidos', methods: ['GET'])]
    public function relatorioAbatidos(AnimalRepository $animalRepository)
    {
        $animaisAbatidos = $animalRepository->getAnimaisAbatidos();

        return $this->render('animal/relatorio_animais_abatidos.html.twig', [
            'animals' => $animaisAbatidos,
        ]);
        
    }

    #[Route('/leite', name: 'animal_quantidade_leite', methods: ['GET'])]
    public function relatorioLeiteSemana(AnimalRepository $animalRepository): Response
    {
        $animaisLeite = $animalRepository->calcularProducaoTotalLeite();

        return $this->render('animal/relatorio_quantidade_leite.html.twig', [
            'leite' => $animaisLeite,
        ]);
    }

    #[Route('/racao', name: 'animal_quantidade_racao', methods: ['GET'])]
    public function relatorioRacaoSemana(AnimalRepository $animalRepository): Response
    {
        $animaisRacao = $animalRepository->calcularRacao();

        return $this->render('animal/relatorio_quantidade_racao.html.twig', [
            'racao' => $animaisRacao,
        ]);
    }

    #[Route('/bezerros', name: 'animal_quantidade_bezerros', methods: ['GET'])]
    public function relatorioAnimaisBezerros(AnimalRepository $animalRepository): Response
    {
        $animaisBezerros = $animalRepository->findAnimaisJovensConsumoAlto();

        return $this->render('animal/relatorio_animais_bezerros.html.twig', [
            'bezerros' => $animaisBezerros,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_abate', methods: ['GET', 'POST'])]
    public function abate(int $id, AnimalRepository $animalRepository, Request $request)
    {
        $animal = $animalRepository->find($id);

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($animal->isAbate() === false) {
            $animal->setAbate(true);
            $animalRepository->save($animal, true);
            $this->addFlash('success', 'Animal atualizado com sucesso!');
        } else {
            $this->addFlash('warning', 'Animal já está abatido!');
        }
        return $this->redirectToRoute('animal_relatorioAnimais');
        
    }

  

    
}

<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController {

    /**
     * @var \App\Repository\NewsRepository
     */
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository) {
        $this->newsRepository = $newsRepository;
    }

    public function index(Request $request): Response {
        $offset = max(0, $request->query->getInt('offset', 0));

        $news = $this->newsRepository->findAll();
        $paginator = $this->newsRepository->getNewsPaginator($news, $offset);

        $form = $this->createForm(NewsType::class, (new News()), [
            'action' => $this->generateUrl('save'),
            'method' => 'POST',
        ]);

        return $this->render('blog/index.html.twig', [
            'newsList' => $paginator,
            'form' => $form->createView(),
            'previous' => $offset - NewsRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + NewsRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    public function show(int $id) {
        /** @var News $news */
        $news = $this->newsRepository->find($id);

        return $this->render('blog/show.html.twig', [
            'news' => $news,
        ]);
    }

    public function save(Request $request) {
        $form = $this->createForm(NewsType::class, (new News()));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
    }
}
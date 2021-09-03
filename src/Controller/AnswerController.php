<?php
/**
 * Answer Controller
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Flex\PackageFilter;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnswerController.
 *
 * @Route("/answer")
 */

class AnswerController extends AbstractController
{
    private $answerRepository;
    private $answer;
    private $paginator;

    /**
     * AnswerController constructor
     *
     * @param \App\Repository\AnswerRepository $answerRepository Answer Repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     */
    public function __construct(AnswerRepository $answerRepository, PaginatorInterface $paginator)
    {
        $this->answerRepository = $answerRepository;
        $this->paginator = $paginator;
    }

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @return \Symfony\Component\HttpFoundation\Response               HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="answer_index",
     * )
     */
    public function index(Request $request, PaginatorInterface $paginator, AnswerRepository $answerRepository): Response
    {
        $pagination = $paginator->paginate(
            $answerRepository->queryAll(),
            $request->query->getInt('page', 1),
            AnswerRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'answer/index.html.twig',
            ['pagination' => $pagination]
        );
    }
    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @param \App\Repository\AnswerRepository $answerRepository Answer repository
     * @param \App\Entity\Answer $answer Answer Entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="answer_create",
     * )
     */
    public function create(Request $request, AnswerRepository $answerRepository, Answer $answer): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answerRepository->save($answer);

            $this->addFlash('success', 'answer_created_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'answer/create.html.twig',
            ['form' => $form->createView()]
        );
    }


}
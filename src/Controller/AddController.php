<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AddController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, Security $security): Response
    {
        $task = new Tasks();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTime());
            // @todo security can return null
            $task->setUser($this->getUser());

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('add/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

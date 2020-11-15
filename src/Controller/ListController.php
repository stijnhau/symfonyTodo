<?php

namespace App\Controller;

use App\Entity\Tasks;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $tasks = $this->getDoctrine()
            ->getRepository(Tasks::class)
            ->findAll();

        return $this->render('list/index.html.twig', [
            'tasks' => $tasks
        ]);
    }
}

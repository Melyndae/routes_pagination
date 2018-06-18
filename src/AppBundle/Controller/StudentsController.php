<?php
// src/AppBundle/Controller/StudentsController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentsController extends Controller
{
    /**
     * Matches /index exactly
     *
     * @Route("/index", name="students_index")
     */
    public function indexAction()
    {
        return $this->render('/index.html.twig');
    }

    /**
     * Matches /list/{page} exactly
     *
     * @Route(
     *     "/list/{page}",
     *     name="students_list",
     *     defaults={"page": "1"},
     *     requirements={"page": "\d+"}
     * )
     */
    public function ListAction(Connection $connection, $page, Request $request)
    {

        $limit = $request->query->get('limit', 5);

        $start = ($page * $limit) - $limit;

        $countStudents = $connection->fetchAssoc('SELECT COUNT(id) as nbStudents FROM students');

        $pagesTotal = ceil($countStudents['nbStudents'] / $limit);

        if ($page < 1 OR $page > $pagesTotal) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        $students = $connection->fetchAll('SELECT * FROM students LIMIT '.$start.','.$limit);

        return $this->render('/list.html.twig', array(
            'students' => $students,
            'page' => $page,
            'pagesTotal' => $pagesTotal,
            'limit' => $limit
        ));
    }
}
<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $linkList = $entityManager->getRepository(Link::class)->findAll();

        dd($linkList);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

        /**
     * @Route("/link-add", methods="GET", name="link_add")
     */
    public function linkAdd(): Response
    {
        $link = new Link();
        $link->setName(substr(md5(uniqid(rand(1,6))), 0, 8));
        $link->setUrl('https://test.com');
        $time = new \DateTime();
        $link->setLifetime($time);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($link);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
}

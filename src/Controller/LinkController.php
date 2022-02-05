<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    /**
     * @Route("/", methods="GET|POST", name="homepage")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->add('url', TextType::class)
            ->add('lifetime', TimeType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($link);
            $entityManager->flush();

            return $this->redirectToRoute('link_statistic');
        }

        return $this->render('default/link_home.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/link-statistic", methods="GET", name="link_statistic")
     */
    public function linkStatistic(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $links = $entityManager->getRepository(Link::class)->findAll();

        // dd($links);

        return $this->render('default/link_statistic.html.twig', [
            'links' => $links
        ]);
    }

    /**
     * @Route("/link-edit/{id}", methods="GET|POST", name="link_edit", requirements={"id"="\d+"})
     */
    public function linkEdit(Request $request, int $id = null): Response 
    {

        $entityManager = $this->getDoctrine()->getManager();

        if($id) {
            $link = $entityManager->getRepository(Link::class)->find($id);
        }else {
            $link = new Link();
        }

        $form = $this->createFormBuilder($link)
            ->add('url', TextType::class)
            ->add('lifetime', TimeType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($link);
            $entityManager->flush();

            return $this->redirectToRoute('link_statistic');
        }

        return $this->render('default/link_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

            return $this->redirectToRoute('links_statistic', ['id' => $link->getId()]);
        }

        // dd($linkList);

        return $this->render('default/link.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/links-statistic", methods="GET", name="links_statistic")
     */
    public function linksStatistic(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $links = $entityManager->getRepository(Link::class)->findAll();

        return $this->render('default/links_statistic.html.twig', [
            'links' => $links
        ]);
    }

    /**
     * @Route("/link-edit/{id}", methods="GET|POST", name="link_edit", requirements={"id"="\d+"})
     */
    public function editLink(Request $request, int $id = null): Response 
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

            return $this->redirectToRoute('link_edit', ['id' => $link->getId()]);
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

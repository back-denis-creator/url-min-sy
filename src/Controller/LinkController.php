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

        return $this->render('default/link_statistic.html.twig', [
            'links' => $links
        ]);
    }

    /**
     * @Route("/link/{name}", methods="GET|POST", name="link_transition",)
     */
    public function linkTransition(Request $request, string $name = ''): Response 
    {

        $entityManager = $this->getDoctrine()->getManager();
        $link = null;

        if($name) {
            $link = $entityManager->getRepository(Link::class)->findOneBy(['name' => (string) $name]);
            
            //Время которое уже прожила ссылка;
            $timeLife = time() - $link->getCreateAt()->format('U');
   
            //Время жизни -> unix
            $unixTimeLife = explode(":", $link->getLifetime()->format('H:m:i'));
            $unixTimeLife[0] = $unixTimeLife[0] * 60 * 60;
            $unixTimeLife[1] = $unixTimeLife[1] * 60;
            $unixTimeLife = $unixTimeLife[0] + $unixTimeLife[1] + $unixTimeLife[2];

            //Если ссылка прожила больше, положенного ей времени
            if($timeLife < $unixTimeLife) {
                $link->setTransitions($link->getTransitions() + 1);
                $entityManager->persist($link);
                $entityManager->flush();
            }else {
                $entityManager->remove($link);
                $entityManager->flush();
            }
        }

        if(empty($link)) {
            return $this->redirectToRoute('homepage');
        }

        return $this->redirect($link->getUrl());
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

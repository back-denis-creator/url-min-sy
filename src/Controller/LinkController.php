<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\EditLinkFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine) {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/", methods="GET|POST", name="homepage")
     */
    public function index(Request $request): Response
    {
        $link = new Link();

        $form = $this->createForm(EditLinkFormType::class, $link);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($link);
            $this->entityManager->flush();

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
        $links = $this->entityManager->getRepository(Link::class);

        $query = $links->createQueryBuilder('l');
        $query = $query->orderBy('l.id', 'DESC')->getQuery();
       
        $entities = $query->getResult();

        return $this->render('default/link_statistic.html.twig', [
            'links' => $entities
        ]);
    }

    /**
     * @Route("/link/{name}", methods="GET|POST", name="link_transition",)
     */
    public function linkTransition(Request $request, string $name = ''): Response 
    {
        $link = $this->entityManager->getRepository(Link::class)->findOneBy(['name' => (string) $name]) ?? null;

        if($link) {
            $timeLife = time() - $link->getCreateAt()->format('U');
   
            $unixTimeLife = explode(":", $link->getLifetime()->format('H:m:i'));
            $unixTimeLife[0] = $unixTimeLife[0] * 60 * 60;
            $unixTimeLife[1] = $unixTimeLife[1] * 60;
            $unixTimeLife = $unixTimeLife[0] + $unixTimeLife[1] + $unixTimeLife[2];

            if($timeLife < $unixTimeLife && $unixTimeLife > 60) {
                $link->setTransitions($link->getTransitions() + 1);
                $this->entityManager->persist($link);
                $this->entityManager->flush();
            }else {
                $this->entityManager->remove($link);
                $this->entityManager->flush();
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
        if($id) {
            $link = $this->entityManager->getRepository(Link::class)->find($id);
        }else {
            $link = new Link();
        }

        $form = $this->createForm(EditLinkFormType::class, $link);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($link);
            $this->entityManager->flush();

            return $this->redirectToRoute('link_statistic');
        }

        return $this->render('default/link_edit.html.twig', [
            'form' => $form->createView(),
            'id' => $link->getId()
        ]);
    }

    /**
     * @Route("/link-delete/{id}", methods="GET", name="link_delete", requirements={"id"="\d+"})
     */
    public function linkDelete(Request $request, int $id = null): Response 
    {
        if($id) {
            $link = $this->entityManager->getRepository(Link::class)->find($id);
            $this->entityManager->remove($link);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('link_statistic');
    }
}

<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use App\Utility\CursorEncoder;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/country')]
final class CountryController extends AbstractController
{
    #[Route(name: 'app_country_index', methods: ['GET'])]
    public function index(
        Request $request,
        CountryRepository $repository,
        CursorEncoder $cursorEncoder,
    ): Response
    {
        $data = $cursorEncoder->decode(
            $request->query->get('cursor')
        );

        // La recherche vient du champ input (première page) ou du token (pages suivantes)
        // le champ input a la priorité (première soumission du formulaire), sinon on relit depuis le token (pages suivantes via scroll infini).
        $search = $request->query->get('q')
            ?? $data['q']
            ?? null;

        $countries = $repository->findPaginatedBySearch(
            maxResult: 10,
            search: $search,
            cursor: $data,
        );

        $nextCursor = null;

        if (count($countries) === 10) {

            $last = end($countries);

            $nextCursor = $cursorEncoder->encode([
                'country' => $last->getCountry(),
                'code'    => $last->getCode(),
                'q'       => $search,
            ]);
        }

        $params = [
            'countries'  => $countries,
            'nextCursor' => $nextCursor,
            'q'          => $search,
            'action'     => $request->query->has('cursor') ? 'append' : 'replace',
        ];

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {

            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render(
                'country/index/stream.pagination.html.twig',
                $params
            );
        }

        return $this->render('country/index.html.twig', $params);
    }

    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country, [
            'action' => $this->generateUrl('app_country_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($country);
            $entityManager->flush();

            return $this->render('streams/row.html.twig', [
                'entity' => $country,
                'entityName' => 'country',
                'action' => 'prepend',
                'target' => 'country_'.$country->getCode(),
                'q' => '',
            ], new Response('', 200, [
                'Content-Type' => 'text/vnd.turbo-stream.html'
            ]));

            //return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->headers->has('Turbo-Frame') ? 'country/drawer/new.html.twig' : 'country/new_dialog.html.twig';

        return $this->render($template, [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_country_show', methods: ['GET'])]
    public function show(
        Request $request,
        Country $country
    ): Response
    {
        $template = $request->headers->has('Turbo-Frame') ? 'country/drawer/show.html.twig' : 'country/show.html.twig';

        return $this->render($template, [
            'country' => $country,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Country $country,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(CountryType::class, $country, [
            'action' => $this->generateUrl('app_country_edit', [ 'id' => $country->getCode() ])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->render('streams/row.html.twig', [
                'entity' => $country,
                'entityName' => 'country',
                'action' => 'replace',
                'target' => 'country_'.$country->getCode(),
                'q' => '',
            ], new Response('', 200, [
                'Content-Type' => 'text/vnd.turbo-stream.html'
            ]));

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->headers->has('Turbo-Frame') ? 'country/drawer/edit.html.twig' : 'country/edit.html.twig';

        return $this->render($template, [
            'country' => $country,
            'form' => $form,
        ]);
    }

    /**
     *
     */
    #[Route('/{id}/delete/dialog', name: 'app_country_delete_dialog', methods: ['POST'])]
    public function delete_dialog(
        Request $request,
        Country $country,
        EntityManagerInterface $entityManager
    ): Response {
            return $this->render('streams/dialog.html.twig', [
                'content' => 'h',
            ], new Response('', 200, [
                'Content-Type' => 'text/vnd.turbo-stream.html'
            ]));
    }

    /**
     * Delete
     */
    #[Route('/{id}/delete', name: 'app_country_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Country $country,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$country->getCode(), $request->getPayload()->getString('_token'))) {
            return $this->render('dialog/error.html.twig', [
                'message' => 'Token CSRF invalide.'
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        try {
            $id = $country->getCode();
            $entityManager->remove($country);
            $entityManager->flush();

            return $this->render('streams/remove.html.twig', [
                'action' => 'remove',
                'target' => 'country_'.$country->getCode(),
            ], new Response('', 200, [
                'Content-Type' => 'text/vnd.turbo-stream.html'
            ]));

        } catch (\Exception $e) {

            return $this->render('dialog/error.html.twig', [
                'message' => 'Suppression impossible : ce pays est utilisé.'
            ], new Response('', Response::HTTP_CONFLICT));
        }

        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Dto\JobView;
use App\Job\JobFake;
use App\Job\JobLogger;
use App\Job\JobManager;
use App\Message\DemoMessage;
use App\Message\ImportMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Symfony\Component\Notifier\Bridge\Ntfy\NtfyOptions;
use Symfony\Component\Uid\Uuid;

final class DemoController extends AbstractController
{
    /**
     * Page d'accueil des démos
     */
    #[Route('/demo', name: 'demo')]
    public function index(): Response
    {
        return $this->render('demo/index.html.twig');
    }

    /**
     * Session
     * Stockage d'une valeur dans une variable de session.
     * Redirection vers une autre route.
     */
    #[Route('/demo/session', name: 'demo_session', methods:['POST'])]
    #[IsCsrfTokenValid('demo_session', tokenKey: '_csrf_token')]
    public function session(
        Request $request,
        CacheInterface $cache,
        ): Response
    {
        $session = $request->getSession();
        $session->set('couleur', 'rouge');

        return $this->redirectToRoute('demo_session_success');
    }

    /**
     * Récupération de la variable de session.
     * Lecture d'une valeur en cache. Si elle n'est pas présente, la fixer avec une temporisation de 15 secondes.
     */
    #[Route('/demo/session_success', name: 'demo_session_success')]
    public function session_success(
        Request $request,
        CacheInterface $cache,
        ): Response
    {
        $session = $request->getSession();

        $value = $cache->get('statistics', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            sleep(15);
            return ['resultat' => 73];
        });

        return $this->render('demo/session.html.twig', [ 'couleur' => $session->get('couleur'), 'value' => $value['resultat'] ]);
    }

    /**
     * Monolog
     * Écrire des messages dans le journal des événments.
     * La sortie est dirigée vers la console stderr, interceptée par le composant Vector qui envoie vers VictoriaLogs.
     */
    #[Route('/demo/logger', name: 'demo_logger', methods:['POST'])]
    #[IsCsrfTokenValid('demo_logger', tokenKey: '_csrf_token')]
    public function logger(
        LoggerInterface $logger,
    ): Response
    {
        $logger->debug('Message de débogage');
        $logger->info('Message d\'information');
        $logger->warning('Message d\'avertissement');
        $logger->error('Message d\'erreur');
        $logger->critical('Message critique', [
            'cause' => 'inconnue'
        ]);

        return $this->redirectToRoute('demo_logger_success');
    }

    /**
     * Page de résultat de l'appel. Redirection après POST (PRG), Post-Redirect-Get pour éviter les appels intempestifs sur rechargement de la page
     */
    #[Route('/demo/logger_success', name: 'demo_logger_success')]
    public function logger_success(): Response
    {
        return $this->render('demo/logger.html.twig');
    }

    /**
     * Dialog
     */
    #[Route('/demo/dialog', name: 'demo_dialog', methods: ['POST'])]
    public function dialog(): Response
    {
        return $this->render('streams/demo.html.twig', [
                'content' => 'demo',
        ], new Response('', 200, [
            'Content-Type' => 'text/vnd.turbo-stream.html'
        ]));
    }

    /**
     * Démo Symfony Messenger.
     * Envoi d'un message sur le bus de communication. Dans notre cas Redis
     */
    #[Route('/demo/messenger', name: 'demo_messenger', methods: ['POST'])]
    public function messenger(
        MessageBusInterface $bus,
    ): Response
    {
        $bus->dispatch(new DemoMessage('Dans la communication, le plus compliqué n\'est ni le message, ni la technique, mais le récepteur.'));

        return $this->redirectToRoute('demo_messenger_success');
    }

    /**
     * PRG pour la démo de Messenger
     */
    #[Route('/demo/messenger/success', name: 'demo_messenger_success')]
    public function messenger_success(): Response
    {
        return $this->render('demo/messenger.html.twig');
    }

    /**
     * Symfony Mailer
     */
    #[Route('/demo/mailer', name: 'demo_mailer', methods:['POST'])]
    #[IsCsrfTokenValid('demo_mailer', tokenKey: '_csrf_token')]
    public function mailer(
        LoggerInterface $logger,
        MailerInterface $mailer,
    ): Response
    {
         $email = (new TemplatedEmail())
            ->to('you@example.com')
            ->subject('Test de Symfony Mailer')
            ->text('Message au format texte.')
            ->htmlTemplate('demo/mailer_message.html.twig')
            ->context([
                'couleur' => 'bleu',
            ]);

        $mailer->send($email);

        $logger->info('Message envoyé');

        return $this->redirectToRoute('demo_mailer_success');
    }

    /**
     * PRG pour la démo de Mailer
     */
    #[Route('/demo/mailer/success', name: 'demo_mailer_success')]
    public function mailer_success(): Response
    {
        return $this->render('demo/mailer.html.twig', [
            'controller_name' => 'DemoController',
        ]);
    }

    /**
     * Démo pour le style des éléments de formulaire
     */
    #[Route('/demo/form', name: 'demo_form')]
    public function form(): Response
    {
        return $this->render('demo/form.html.twig');
    }

    #[Route('/demo/job/logger', name: 'demo_job_logger')]
    public function job_logger(
        JobManager $jobManager,
        SerializerInterface $serializer,
    ): Response
    {
        $job = $jobManager->dispatch(JobLogger::class,  ["auteur" => "Otto West"], "C'était un test !");

        return $this->json(JobView::fromEntity($job));

        /*
        return $this->json($serializer->serialize(
            JobView::fromEntity($job),
            'json'
        ));
        */
    }

    #[Route('/demo/job/fake', name: 'demo_job_fake')]
    public function job_fake(
        JobManager $jobManager,
        SerializerInterface $serializer,
    ): Response
    {
        $job = $jobManager->dispatch(JobFake::class,  ["behavior" => "fail"]);

        return $this->json($serializer->serialize(
            JobView::fromEntity($job),
            'json'
        ));
    }

    #[Route('/demo/notifier', name: 'demo_notifier', methods: ['POST'])]
    public function notifier(
        TexterInterface $texter
    ):Response {

        $message = new PushMessage(
            '3e test',
            'Contenu'
        );

        $texter->send($message);

        return $this->redirectToRoute('demo_notifier_success');
    }

    #[Route('/demo/notifier/success', name: 'demo_notifier_success')]
    public function notifier_success(): Response
    {
        return $this->render('demo/notifier.html.twig');
    }

    #[Route('/demo/async', name: 'demo_async')]
    public function async(
        Request $request,
        MessageBusInterface $bus
    ): Response
    {
        $form = $this->createFormBuilder()
            ->add('csv', FileType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $importId = Uuid::v4();
            $content = (string) file_get_contents($form->get('csv')->getData()->getPathname());

            $bus->dispatch(new ImportMessage($importId, $content));

            $this->addFlash('success', 'The file will be imported ASAP.');

            return $this->redirectToRoute('demo_async', [
                'importId' => $importId,
            ]);
        }

        return $this->render('demo/async.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

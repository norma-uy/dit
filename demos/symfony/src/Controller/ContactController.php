<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Page;
use App\Entity\PageTranslations;
use App\Form\ContactType;
use App\Repository\PageRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}')]
class ContactController extends AbstractController
{
    public function __construct(private PageRepository $pageRepository, private MailerInterface $mailer)
    {
    }

    #[
        Route(
            [
                'es' => '/contacto',
                'en' => '/contact',
            ],
            name: 'contact-page',
        ),
    ]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();

        $contactPage = $this->pageRepository->findOneBySlugs(['contact', 'contacto']);

        /**
         * @var Page
         */
        $contactPageLocale = $contactPage
            ->getTranslations()
            ->filter(function (PageTranslations $pageTrans) use ($locale) {
                return $pageTrans->getLanguageCode() === $locale;
            })
            ->first();

        $contact = new Contact();

        $contactForm = $this->createForm(ContactType::class, $contact);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $from_email = $this->getParameter('email_config.from_email');
            $from_name = $this->getParameter('email_config.from_name');

            $to_email = $this->getParameter('email_config.contact_to_email');
            $to_name = $this->getParameter('email_config.contact_to_name');

            $email = (new TemplatedEmail())
                ->from(new Address($from_email, $from_name))
                ->to(new Address($to_email, $to_name))
                ->replyTo(new Address($contact->getEmail()))
                ->subject('Contact | Blog Website')

                // path of the Twig template to render
                ->htmlTemplate('emails/contact.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'contact' => $contact,
                ]);

            try {
                $this->mailer->send($email);

                $this->addFlash('contactSuccessMsg', 'Contacto enviado con éxito');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('contactErrorMsg', 'Error al enviar el contacto, por favor intente más tarde.');
            }

            return $this->redirectToRoute('contact-page');
        }

        return $this->render('contact/index.html.twig', [
            'content' =>
            $contactPageLocale && !empty($contactPageLocale->getContent())
                ? $contactPageLocale->getContent()
                : $contactPage->getContent(),
            'contactForm' => $contactForm,
        ]);
    }
}

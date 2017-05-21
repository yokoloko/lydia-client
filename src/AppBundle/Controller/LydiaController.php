<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class LydiaController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $user = new User();
        $builder = $this->createFormBuilder($user);

        $builder->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class);

        $builder->add('save', SubmitType::class, array('label' => 'Create User'));

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $lydia = $this->get('lydia');
            $result = $lydia->register($user);

            $user->setPublicToken($result->getPublicToken());

            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();

            $request = new \AppBundle\Entity\Request($user);
            $request->setAmount(12.00);
            $request->setCurrency('EUR');

            $result = $lydia->request($request);

            $request->setRequestUuid($result->getRequestUuid());
            $request->setRequestId($result->getRequestId());
            $request->setUrl($result->getUrl());

            $em = $this->getDoctrine()->getManager();
            $em->persist($request);
            $em->flush();

            return $this->redirectToRoute('status', ['id' => $request->getId()]);
        }

        $users = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findAll();

        return $this->render('default/user.html.twig', [
            'form' => $form->createView(),
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/{id}", name="make_request")
     * @param User $user
     * @param Request $httpRequest
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestAction(User $user, Request $httpRequest)
    {
        $request = new \AppBundle\Entity\Request($user);

        $builder = $this->createFormBuilder($request);
        $builder->add('amount', TextType::class)
            ->add('currency', ChoiceType::class, ['choices' => ['EUR' => 'EUR']])
            ->add('submit', SubmitType::class, array('label' => 'Request this payment'));

        $form = $builder->getForm();

        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $request = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($request);
            $em->flush();

            $lydia = $this->get('lydia');
            $result = $lydia->request($request);

            $request->setRequestUuid($result->getRequestUuid());
            $request->setRequestId($result->getRequestId());
            $request->setUrl($result->getUrl());

            $em = $this->getDoctrine()->getManager();
            $em->persist($request);
            $em->flush();

            return $this->redirectToRoute('status', ['id' => $request->getId()]);
        }

        return $this->render('default/request.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/status/{id}", name="status")
     * @param \AppBundle\Entity\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statusAction(\AppBundle\Entity\Request $request)
    {
        $status = $this->get('lydia')->status($request);

        return $this->render('default/status.html.twig', [
            'request' => $request,
            'status'  => $status
        ]);
    }

    /**
     * @Route("/requests", name="requests")
     */
    public function requests()
    {
        $requests = $this->getDoctrine()->getManager()->getRepository('AppBundle:Request')->findAll();

        return $this->render('default/requests.html.twig', [
            'requests' => $requests
        ]);
    }
}
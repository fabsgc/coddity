<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Support;
use AppBundle\Form\Type\SupportType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="home")
	 * @param Request $request
	 * @return Response
	 */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $professions = $this->get('app.professions')->getAll();

        return $this->render('AppBundle:Home:index.html.twig', [
            'professions' => $professions
        ]);
    }

	/**
	 * @Route("/faq", name="qa")
     * @Method({"GET"})
	 * @param Request $request
	 * @return Response
	 */
	public function indexQaAction(Request $request)
	{
		return $this->render('AppBundle:Home:qa.html.twig', array());
	}

    /**
     * @Route("/terms", name="terms")
     * @param Request $request
     * @return Response
     */
    public function termsAction(Request $request)
    {
        return $this->render('AppBundle:Home:terms.html.twig');
    }
}

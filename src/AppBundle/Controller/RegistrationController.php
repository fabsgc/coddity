<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\HelpRegistration;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Profession;
use AppBundle\Entity\Professional;
use AppBundle\Entity\ProfessionalSettings;
use AppBundle\Entity\Registration\StepFour;
use AppBundle\Entity\Registration\StepOne;
use AppBundle\Entity\Registration\StepThree;
use AppBundle\Entity\Registration\StepTwo;
use AppBundle\Entity\Specialization;
use AppBundle\Entity\User;
use AppBundle\Form\Type\Registration\Customer\UserType;
use AppBundle\Form\Type\Registration\HelpRegistrationType;
use AppBundle\Form\Type\Registration\Professional\StepFourType;
use AppBundle\Form\Type\Registration\Professional\StepOneType;
use AppBundle\Form\Type\Registration\Professional\StepThreeType;
use AppBundle\Form\Type\Registration\Professional\StepTwoType;
use AppBundle\Service\Subscriptions;
use AppBundle\Util\Now;
use DateTime;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session;

/**
 * @Route("/register")
 */
class RegistrationController extends Controller
{

}
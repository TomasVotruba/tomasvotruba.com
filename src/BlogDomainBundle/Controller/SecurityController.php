<?php

/*
 * This file is part of Tomasvotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace BlogDomainBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(EngineInterface $templating, AuthenticationUtils $authenticationUtils)
    {
        $this->templating = $templating;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function loginAction()
    {
        return $this->templating->renderResponse('@BlogDomainBundle/security/login.html.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * This is the route the login form submits to.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the login automatically. See form_login in app/config/security.yml
     *
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
        throw new Exception('This should never be reached!');
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new Exception('This should never be reached!');
    }
}

//
//final class BlogController
//{
//    /**
//     * @var EngineInterface
//     */
//    private $templating;
//
//    public function __construct(EngineInterface $templating)
//    {
//        $this->templating = $templating;
//    }
//
//    public function indexAction()
//    {
//        // todo: find all posts
//        return $this->templating->renderResponse('@BlogDomainBundle/blog/index.html.twig', [
//            'posts' => []
//        ]);
//    }
//}

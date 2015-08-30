<?php

/* @BlogDomainBundle/security/login.html.twig */
class __TwigTemplate_1c0e1879b1245bb078be9b459906ce50d980a1dd9af6f3b8df273c4df6057225 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "@BlogDomainBundle/security/login.html.twig", 1);
        $this->blocks = array(
            'body_id' => array($this, 'block_body_id'),
            'main' => array($this, 'block_main'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_0c2607f7d67cf8459ce334f5649d6c2f79ab527b4c89e4d53e0700806cf87d5c = $this->env->getExtension("native_profiler");
        $__internal_0c2607f7d67cf8459ce334f5649d6c2f79ab527b4c89e4d53e0700806cf87d5c->enter($__internal_0c2607f7d67cf8459ce334f5649d6c2f79ab527b4c89e4d53e0700806cf87d5c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@BlogDomainBundle/security/login.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_0c2607f7d67cf8459ce334f5649d6c2f79ab527b4c89e4d53e0700806cf87d5c->leave($__internal_0c2607f7d67cf8459ce334f5649d6c2f79ab527b4c89e4d53e0700806cf87d5c_prof);

    }

    // line 3
    public function block_body_id($context, array $blocks = array())
    {
        $__internal_2342c4659b17c47ffa3f37fff90558c5e04feaa00ea81a04eafe0e3b245078b4 = $this->env->getExtension("native_profiler");
        $__internal_2342c4659b17c47ffa3f37fff90558c5e04feaa00ea81a04eafe0e3b245078b4->enter($__internal_2342c4659b17c47ffa3f37fff90558c5e04feaa00ea81a04eafe0e3b245078b4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body_id"));

        echo "login";
        
        $__internal_2342c4659b17c47ffa3f37fff90558c5e04feaa00ea81a04eafe0e3b245078b4->leave($__internal_2342c4659b17c47ffa3f37fff90558c5e04feaa00ea81a04eafe0e3b245078b4_prof);

    }

    // line 5
    public function block_main($context, array $blocks = array())
    {
        $__internal_8314ee17777c674a6e96095fe8731fb3f739472cf4f4507de45d8162ac248168 = $this->env->getExtension("native_profiler");
        $__internal_8314ee17777c674a6e96095fe8731fb3f739472cf4f4507de45d8162ac248168->enter($__internal_8314ee17777c674a6e96095fe8731fb3f739472cf4f4507de45d8162ac248168_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "main"));

        // line 6
        echo "        ";
        if ((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error"))) {
            // line 7
            echo "            <div class=\"alert alert-danger\">
                ";
            // line 8
            echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "messageKey", array()), $this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "messageData", array()), "security"), "html", null, true);
            echo "
            </div>
        ";
        }
        // line 11
        echo "
        <div class=\"row\">
            <div class=\"col-sm-5\">
                <div class=\"well\">
                    <form action=\"";
        // line 15
        echo $this->env->getExtension('routing')->getPath("security_login_check");
        echo "\" method=\"post\">
                        <fieldset>
                            <legend><i class=\"fa fa-lock\"></i> ";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("title.login"), "html", null, true);
        echo "</legend>
                            <div class=\"form-group\">
                                <label for=\"username\">";
        // line 19
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("label.username"), "html", null, true);
        echo "</label>
                                <input type=\"text\" id=\"username\" name=\"_username\" value=\"";
        // line 20
        echo twig_escape_filter($this->env, (isset($context["last_username"]) ? $context["last_username"] : $this->getContext($context, "last_username")), "html", null, true);
        echo "\" class=\"form-control\"/>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"password\">";
        // line 23
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("label.password"), "html", null, true);
        echo "</label>
                                <input type=\"password\" id=\"password\" name=\"_password\" class=\"form-control\" />
                            </div>
                            <input type=\"hidden\" name=\"_csrf_token\" value=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderCsrfToken("authenticate"), "html", null, true);
        echo "\"/>
                            <button type=\"submit\" class=\"btn btn-primary\">
                                <i class=\"fa fa-sign-in\"></i> ";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("action.sign_in"), "html", null, true);
        echo "
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>

            <div id=\"login-help\" class=\"col-sm-7\">
                <h3>
                    <i class=\"fa fa-long-arrow-left\"></i>
                    ";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("help.login_users"), "html", null, true);
        echo "
                </h3>

                <table class=\"table table-striped table-bordered table-hover\">
                    <thead>
                    <tr>
                        <th>";
        // line 44
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("label.username"), "html", null, true);
        echo "</th>
                        <th>";
        // line 45
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("label.password"), "html", null, true);
        echo "</th>
                        <th>";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("label.role"), "html", null, true);
        echo "</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>john_user</td>
                        <td>kitten</td>
                        <td><code>ROLE_USER</code> (";
        // line 53
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("help.role_user"), "html", null, true);
        echo ")</td>
                    </tr>
                    <tr>
                        <td>anna_admin</td>
                        <td>kitten</td>
                        <td><code>ROLE_ADMIN</code> (";
        // line 58
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("help.role_admin"), "html", null, true);
        echo ")</td>
                    </tr>
                    </tbody>
                </table>

                <div id=\"login-users-help\" class=\"panel panel-default\">
                    <div class=\"panel-body\">
                        <p>
                            <span class=\"label label-success\">";
        // line 66
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("note"), "html", null, true);
        echo "</span>
                            ";
        // line 67
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("help.reload_fixtures"), "html", null, true);
        echo "<br/>

                            <code class=\"console\">\$ php app/console doctrine:fixtures:load</code>
                        </p>

                        <p>
                            <span class=\"label label-success\">";
        // line 73
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("tip"), "html", null, true);
        echo "</span>
                            ";
        // line 74
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans("help.add_user"), "html", null, true);
        echo "<br/>

                            <code class=\"console\">\$ php app/console app:add-user</code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    ";
        
        $__internal_8314ee17777c674a6e96095fe8731fb3f739472cf4f4507de45d8162ac248168->leave($__internal_8314ee17777c674a6e96095fe8731fb3f739472cf4f4507de45d8162ac248168_prof);

    }

    // line 84
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_2e64341fdd0b4de5b678d10895076f74775f14f5b11f542fd67d5d5817b76af8 = $this->env->getExtension("native_profiler");
        $__internal_2e64341fdd0b4de5b678d10895076f74775f14f5b11f542fd67d5d5817b76af8->enter($__internal_2e64341fdd0b4de5b678d10895076f74775f14f5b11f542fd67d5d5817b76af8_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 85
        echo "        ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "

        <script>
            \$(document).ready(function() {
                \$('#username').val('anna_admin');
                \$('#password').val('kitten');
            });
        </script>
    ";
        
        $__internal_2e64341fdd0b4de5b678d10895076f74775f14f5b11f542fd67d5d5817b76af8->leave($__internal_2e64341fdd0b4de5b678d10895076f74775f14f5b11f542fd67d5d5817b76af8_prof);

    }

    public function getTemplateName()
    {
        return "@BlogDomainBundle/security/login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  201 => 85,  195 => 84,  179 => 74,  175 => 73,  166 => 67,  162 => 66,  151 => 58,  143 => 53,  133 => 46,  129 => 45,  125 => 44,  116 => 38,  103 => 28,  98 => 26,  92 => 23,  86 => 20,  82 => 19,  77 => 17,  72 => 15,  66 => 11,  60 => 8,  57 => 7,  54 => 6,  48 => 5,  36 => 3,  11 => 1,);
    }
}

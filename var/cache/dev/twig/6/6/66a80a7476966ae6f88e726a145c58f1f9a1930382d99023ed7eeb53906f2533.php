<?php

/* base.html.twig */
class __TwigTemplate_66a80a7476966ae6f88e726a145c58f1f9a1930382d99023ed7eeb53906f2533 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'header' => array($this, 'block_header'),
            'header_navigation_links' => array($this, 'block_header_navigation_links'),
            'body' => array($this, 'block_body'),
            'main' => array($this, 'block_main'),
            'sidebar' => array($this, 'block_sidebar'),
            'footer' => array($this, 'block_footer'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_53652636b773c0fb03f560a58a3734e309a9eeb9a2d0bbc5e4f60be825c76134 = $this->env->getExtension("native_profiler");
        $__internal_53652636b773c0fb03f560a58a3734e309a9eeb9a2d0bbc5e4f60be825c76134->enter($__internal_53652636b773c0fb03f560a58a3734e309a9eeb9a2d0bbc5e4f60be825c76134_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>

        ";
        // line 7
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 10
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('asset')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    </head>

    <body>

        ";
        // line 15
        $this->displayBlock('header', $context, $blocks);
        // line 48
        echo "
        <div class=\"container body-container\">
            ";
        // line 50
        $this->displayBlock('body', $context, $blocks);
        // line 75
        echo "        </div>

        ";
        // line 77
        $this->displayBlock('footer', $context, $blocks);
        // line 95
        echo "
        ";
        // line 96
        $this->displayBlock('javascripts', $context, $blocks);
        // line 114
        echo "
    </body>
</html>
";
        
        $__internal_53652636b773c0fb03f560a58a3734e309a9eeb9a2d0bbc5e4f60be825c76134->leave($__internal_53652636b773c0fb03f560a58a3734e309a9eeb9a2d0bbc5e4f60be825c76134_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_2cd64d7b46f65e06cd81135fd2c0c151eb974523491ea6cfe327af17811f58b8 = $this->env->getExtension("native_profiler");
        $__internal_2cd64d7b46f65e06cd81135fd2c0c151eb974523491ea6cfe327af17811f58b8->enter($__internal_2cd64d7b46f65e06cd81135fd2c0c151eb974523491ea6cfe327af17811f58b8_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo "TomasVotruba.cz - SOLID Open Source lover!";
        
        $__internal_2cd64d7b46f65e06cd81135fd2c0c151eb974523491ea6cfe327af17811f58b8->leave($__internal_2cd64d7b46f65e06cd81135fd2c0c151eb974523491ea6cfe327af17811f58b8_prof);

    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_03f0a52fd4b5f9c224d15426f5b77ec32982013bd0b85152e77d6f57d48905e6 = $this->env->getExtension("native_profiler");
        $__internal_03f0a52fd4b5f9c224d15426f5b77ec32982013bd0b85152e77d6f57d48905e6->enter($__internal_03f0a52fd4b5f9c224d15426f5b77ec32982013bd0b85152e77d6f57d48905e6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 8
        echo "            <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('asset')->getAssetUrl("css/app.css"), "html", null, true);
        echo "\">
        ";
        
        $__internal_03f0a52fd4b5f9c224d15426f5b77ec32982013bd0b85152e77d6f57d48905e6->leave($__internal_03f0a52fd4b5f9c224d15426f5b77ec32982013bd0b85152e77d6f57d48905e6_prof);

    }

    // line 15
    public function block_header($context, array $blocks = array())
    {
        $__internal_c17b37d9340dfd71257ee6693904457cd3278b34656f9f3956c1879ee7648761 = $this->env->getExtension("native_profiler");
        $__internal_c17b37d9340dfd71257ee6693904457cd3278b34656f9f3956c1879ee7648761->enter($__internal_c17b37d9340dfd71257ee6693904457cd3278b34656f9f3956c1879ee7648761_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "header"));

        // line 16
        echo "            <header>
                <div class=\"navbar navbar-default navbar-static-top\" role=\"navigation\">
                    <div class=\"container\">
                        <div class=\"navbar-header\">
                            <a class=\"navbar-brand\" href=\"";
        // line 20
        echo $this->env->getExtension('routing')->getPath("homepage");
        echo "\">
                                Symfony Demo Application
                            </a>

                            <button type=\"button\" class=\"navbar-toggle\"
                                    data-toggle=\"collapse\"
                                    data-target=\".navbar-collapse\">
                                <span class=\"sr-only\">Toggle navigation</span>
                                <span class=\"icon-bar\"></span>
                                <span class=\"icon-bar\"></span>
                                <span class=\"icon-bar\"></span>
                            </button>
                        </div>
                        <div class=\"navbar-collapse collapse\">
                            <ul class=\"nav navbar-nav navbar-right\">
                                ";
        // line 35
        $this->displayBlock('header_navigation_links', $context, $blocks);
        // line 42
        echo "                            </ul>
                        </div>
                    </div>
                </div>
            </header>
        ";
        
        $__internal_c17b37d9340dfd71257ee6693904457cd3278b34656f9f3956c1879ee7648761->leave($__internal_c17b37d9340dfd71257ee6693904457cd3278b34656f9f3956c1879ee7648761_prof);

    }

    // line 35
    public function block_header_navigation_links($context, array $blocks = array())
    {
        $__internal_6d35cb23653f71254e8de48b38b476e2599bc2a7c3c299f29364653189b29e1a = $this->env->getExtension("native_profiler");
        $__internal_6d35cb23653f71254e8de48b38b476e2599bc2a7c3c299f29364653189b29e1a->enter($__internal_6d35cb23653f71254e8de48b38b476e2599bc2a7c3c299f29364653189b29e1a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "header_navigation_links"));

        // line 36
        echo "                                    <li>
                                        <a href=\"";
        // line 37
        echo $this->env->getExtension('routing')->getPath("homepage");
        echo "\">
                                            <i class=\"fa fa-home\"></i> Homepage
                                        </a>
                                    </li>
                                ";
        
        $__internal_6d35cb23653f71254e8de48b38b476e2599bc2a7c3c299f29364653189b29e1a->leave($__internal_6d35cb23653f71254e8de48b38b476e2599bc2a7c3c299f29364653189b29e1a_prof);

    }

    // line 50
    public function block_body($context, array $blocks = array())
    {
        $__internal_3c4515620a77551718daedb9555384664cc846fff18eddfe31c248ce34cfc829 = $this->env->getExtension("native_profiler");
        $__internal_3c4515620a77551718daedb9555384664cc846fff18eddfe31c248ce34cfc829->enter($__internal_3c4515620a77551718daedb9555384664cc846fff18eddfe31c248ce34cfc829_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 51
        echo "                <div class=\"row\">
                    <div id=\"main\" class=\"col-sm-9\">
                        ";
        // line 53
        $this->displayBlock('main', $context, $blocks);
        // line 54
        echo "                    </div>

                    <div id=\"sidebar\" class=\"col-sm-3\">
                        ";
        // line 57
        $this->displayBlock('sidebar', $context, $blocks);
        // line 72
        echo "                    </div>
                </div>
            ";
        
        $__internal_3c4515620a77551718daedb9555384664cc846fff18eddfe31c248ce34cfc829->leave($__internal_3c4515620a77551718daedb9555384664cc846fff18eddfe31c248ce34cfc829_prof);

    }

    // line 53
    public function block_main($context, array $blocks = array())
    {
        $__internal_fd6b5d91c95daf2d0075b78c4acd88bbec3ba43e7a4f58fe89087f0379db00d6 = $this->env->getExtension("native_profiler");
        $__internal_fd6b5d91c95daf2d0075b78c4acd88bbec3ba43e7a4f58fe89087f0379db00d6->enter($__internal_fd6b5d91c95daf2d0075b78c4acd88bbec3ba43e7a4f58fe89087f0379db00d6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "main"));

        
        $__internal_fd6b5d91c95daf2d0075b78c4acd88bbec3ba43e7a4f58fe89087f0379db00d6->leave($__internal_fd6b5d91c95daf2d0075b78c4acd88bbec3ba43e7a4f58fe89087f0379db00d6_prof);

    }

    // line 57
    public function block_sidebar($context, array $blocks = array())
    {
        $__internal_1510033d65da8049c08c5b35e68e2648b635d8f92d81edf3b11e7a32317116a0 = $this->env->getExtension("native_profiler");
        $__internal_1510033d65da8049c08c5b35e68e2648b635d8f92d81edf3b11e7a32317116a0->enter($__internal_1510033d65da8049c08c5b35e68e2648b635d8f92d81edf3b11e7a32317116a0_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "sidebar"));

        // line 58
        echo "                            <div class=\"section about\">
                                <div class=\"well well-lg\">
                                    <p>
                                        tagy...
                                    </p>
                                    <p>
                                        <ul>
                                            <li>Nette vývojář od 2010</li>
                                            <li>Symfony vývojář od 2014</li>
                                        </ul>
                                    </p>
                                </div>
                            </div>
                        ";
        
        $__internal_1510033d65da8049c08c5b35e68e2648b635d8f92d81edf3b11e7a32317116a0->leave($__internal_1510033d65da8049c08c5b35e68e2648b635d8f92d81edf3b11e7a32317116a0_prof);

    }

    // line 77
    public function block_footer($context, array $blocks = array())
    {
        $__internal_eff98cd7fc2e835f2543ea91e6dd3005c18aeb723631a0a9fffe8867586ed034 = $this->env->getExtension("native_profiler");
        $__internal_eff98cd7fc2e835f2543ea91e6dd3005c18aeb723631a0a9fffe8867586ed034->enter($__internal_eff98cd7fc2e835f2543ea91e6dd3005c18aeb723631a0a9fffe8867586ed034_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "footer"));

        // line 78
        echo "            <footer>
                <div class=\"container\">
                    <div class=\"row\">
                        <div id=\"footer-copyright\" class=\"col-md-6\">
                            <p>&copy; ";
        // line 82
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
        echo " - The Symfony Project</p>
                            <p>MIT License</p>
                        </div>
                        <div id=\"footer-resources\" class=\"col-md-6\">
                            <p>
                                <a href=\"https://twitter.com/votrubat\"><i class=\"fa fa-twitter\"></i></a>
                                <a href=\"";
        // line 88
        echo $this->env->getExtension('routing')->getPath("blog_rss");
        echo "\"><i class=\"fa fa-rss\"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        ";
        
        $__internal_eff98cd7fc2e835f2543ea91e6dd3005c18aeb723631a0a9fffe8867586ed034->leave($__internal_eff98cd7fc2e835f2543ea91e6dd3005c18aeb723631a0a9fffe8867586ed034_prof);

    }

    // line 96
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_2a555c85fb66330a0d55ae04cb315d307c39ada4b58b5d9b34161c30351e239d = $this->env->getExtension("native_profiler");
        $__internal_2a555c85fb66330a0d55ae04cb315d307c39ada4b58b5d9b34161c30351e239d->enter($__internal_2a555c85fb66330a0d55ae04cb315d307c39ada4b58b5d9b34161c30351e239d_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 97
        echo "        ";
        // line 105
        echo "
            <script src=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('asset')->getAssetUrl("js/app.js"), "html", null, true);
        echo "\"></script>

            <script>
                \$(document).ready(function() {
                    hljs.initHighlightingOnLoad();
                });
            </script>
        ";
        
        $__internal_2a555c85fb66330a0d55ae04cb315d307c39ada4b58b5d9b34161c30351e239d->leave($__internal_2a555c85fb66330a0d55ae04cb315d307c39ada4b58b5d9b34161c30351e239d_prof);

    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  286 => 106,  283 => 105,  281 => 97,  275 => 96,  261 => 88,  252 => 82,  246 => 78,  240 => 77,  220 => 58,  214 => 57,  203 => 53,  194 => 72,  192 => 57,  187 => 54,  185 => 53,  181 => 51,  175 => 50,  163 => 37,  160 => 36,  154 => 35,  142 => 42,  140 => 35,  122 => 20,  116 => 16,  110 => 15,  100 => 8,  94 => 7,  82 => 5,  72 => 114,  70 => 96,  67 => 95,  65 => 77,  61 => 75,  59 => 50,  55 => 48,  53 => 15,  44 => 10,  42 => 7,  37 => 5,  31 => 1,);
    }
}

<?php

/* @WebProfiler/Profiler/layout.html.twig */
class __TwigTemplate_9c65ca0c41a02ad413787371b0d53d25c316ed2880ebb16b32fc504d9457da80 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/base.html.twig", "@WebProfiler/Profiler/layout.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_4f8967c0f42d4b9f3c774f49cf63c70cd09ddedfffdea2bbc3ad0c8ab8080e3c = $this->env->getExtension("native_profiler");
        $__internal_4f8967c0f42d4b9f3c774f49cf63c70cd09ddedfffdea2bbc3ad0c8ab8080e3c->enter($__internal_4f8967c0f42d4b9f3c774f49cf63c70cd09ddedfffdea2bbc3ad0c8ab8080e3c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/layout.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_4f8967c0f42d4b9f3c774f49cf63c70cd09ddedfffdea2bbc3ad0c8ab8080e3c->leave($__internal_4f8967c0f42d4b9f3c774f49cf63c70cd09ddedfffdea2bbc3ad0c8ab8080e3c_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_43a2d46853ae00ec1b916f44848e13a980228b82e850fcb76fa4551099d97971 = $this->env->getExtension("native_profiler");
        $__internal_43a2d46853ae00ec1b916f44848e13a980228b82e850fcb76fa4551099d97971->enter($__internal_43a2d46853ae00ec1b916f44848e13a980228b82e850fcb76fa4551099d97971_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "
    ";
        // line 5
        echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('routing')->getPath("_wdt", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "position" => "normal")));
        echo "

    <div id=\"content\">
        ";
        // line 8
        $this->loadTemplate("@WebProfiler/Profiler/header.html.twig", "@WebProfiler/Profiler/layout.html.twig", 8)->display(array());
        // line 9
        echo "
        <div id=\"main\">

            <div class=\"clear-fix\">
                <div id=\"collector-wrapper\">
                    ";
        // line 14
        if ((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile"))) {
            // line 15
            echo "                        <div id=\"resume\">
                            <a id=\"resume-view-all\" href=\"";
            // line 16
            echo $this->env->getExtension('routing')->getPath("_profiler_search", array("limit" => 10));
            echo "\">View last 10</a>
                            <a id=\"resume-view-latest\" href=\"";
            // line 17
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => "latest", "panel" => (isset($context["panel"]) ? $context["panel"] : $this->getContext($context, "panel")))), "html", null, true);
            echo "\">View latest</a>
                            <strong>Profile for:</strong>
                            ";
            // line 19
            echo twig_escape_filter($this->env, twig_upper_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "method", array())), "html", null, true);
            echo "
                            ";
            // line 20
            if (twig_in_filter(twig_upper_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "method", array())), array(0 => "GET", 1 => "HEAD"))) {
                // line 21
                echo "                                <a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "url", array()), "html", null, true);
                echo "\" id=\"resume-url\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "url", array()), "html", null, true);
                echo "</a>
                            ";
            } else {
                // line 23
                echo "                                <span id=\"resume-url\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "url", array()), "html", null, true);
                echo "</span>
                            ";
            }
            // line 25
            echo "                            <span class=\"date\">
                                <em>by ";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "ip", array()), "html", null, true);
            echo "</em> at <em>";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "time", array()), "r"), "html", null, true);
            echo "</em>
                            </span>
                        </div>
                    ";
        }
        // line 30
        echo "
                    <div id=\"collector-content\">
                        ";
        // line 32
        $this->loadTemplate("@WebProfiler/Profiler/base_js.html.twig", "@WebProfiler/Profiler/layout.html.twig", 32)->display($context);
        // line 33
        echo "                        ";
        $this->displayBlock('panel', $context, $blocks);
        // line 34
        echo "                    </div>
                </div>
                <div id=\"navigation\">
                    ";
        // line 37
        if (array_key_exists("templates", $context)) {
            // line 38
            echo "                        <ul id=\"menu-profiler\">
                            ";
            // line 39
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["templates"]) ? $context["templates"] : $this->getContext($context, "templates")));
            foreach ($context['_seq'] as $context["name"] => $context["template"]) {
                // line 40
                echo "                                ";
                ob_start();
                echo twig_escape_filter($this->env, $this->getAttribute($context["template"], "renderBlock", array(0 => "menu", 1 => array("collector" => $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "getcollector", array(0 => $context["name"]), "method"))), "method"), "html", null, true);
                $context["menu"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
                // line 41
                echo "                                ";
                if (((isset($context["menu"]) ? $context["menu"] : $this->getContext($context, "menu")) != "")) {
                    // line 42
                    echo "                                    <li class=\"";
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    if (($context["name"] == (isset($context["panel"]) ? $context["panel"] : $this->getContext($context, "panel")))) {
                        echo " selected";
                    }
                    echo "\">
                                        <a href=\"";
                    // line 43
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "panel" => $context["name"])), "html", null, true);
                    echo "\">";
                    echo (isset($context["menu"]) ? $context["menu"] : $this->getContext($context, "menu"));
                    echo "</a>
                                    </li>
                                ";
                }
                // line 46
                echo "                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['template'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 47
            echo "                            <li class=\"minimize\">
                                <a href=\"javascript:void(0);\" title=\"Minimize toolbar\" onclick=\"return toggleMenuPanels();\">
                                    <span class=\"label\">
                                        <span class=\"icon\"><svg id=\"minimizePanelIcon\" width=\"30\" height=\"33\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 30 33\" enable-background=\"new 0 0 30 33\" xml:space=\"preserve\"><path fill=\"#3F3F3F\" d=\"M15 5C8.4 5 3 10.4 3 17c0 6.6 5.4 12 12 12s12-5.4 12-12C27 10.4 21.6 5 15 5z M19.1 21.5l-1.8 1.8L10.9 17 l6.3-6.3l1.8 1.8L14.6 17L19.1 21.5z\"/></svg></span>
                                        <strong>Minimize</strong>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    ";
        }
        // line 57
        echo "                    ";
        echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('routing')->getPath("_profiler_search_bar"));
        echo "
                    ";
        // line 58
        $this->loadTemplate("@WebProfiler/Profiler/admin.html.twig", "@WebProfiler/Profiler/layout.html.twig", 58)->display(array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token"))));
        // line 59
        echo "                </div>
            </div>
        </div>
    </div>

    <script>//<![CDATA[

        function toggleMenuPanels(state, doSave) {
            var menu = document.getElementById('navigation'), savedState = Sfjs.getPreference('menu/displayState'),
                displayState, elem, className;

            if (null === savedState) {
                savedState = 'block';
            }

            displayState = state || ('block' === savedState ? 'none' : 'block');

            if ('undefined' === typeof doSave) {
                doSave = true;
            }

            document.getElementById('searchBar').style.display = displayState;
            document.getElementById('adminBar').style.display = displayState;

            if ('block' === displayState) {
                Sfjs.removeClass(menu, 'collapsed-menu');
                Sfjs.removeClass(menu.parentNode.parentNode, 'collapsed-menu-parents');
            } else {
                Sfjs.addClass(menu, 'collapsed-menu');
                Sfjs.addClass(menu.parentNode.parentNode, 'collapsed-menu-parents');
            }

            if (doSave) {
                Sfjs.setPreference('menu/displayState', displayState);
            }

            try {
                canvasAutoUpdateOnThresholdChange(null);
            } catch (err) {

            }

            return false;
        }

        window.setTimeout(function() {
            if (null === document.getElementById('menu-profiler')) {
                return;
            }

            var menuItems = document.getElementById('menu-profiler').getElementsByTagName('LI'),
                elem, value, child, displayState = Sfjs.getPreference('menu/displayState');

            if (displayState == 'none') {
                toggleMenuPanels('none', false);
            }

            for (elem in menuItems) {
                if (typeof(menuItems[elem].children) !== 'undefined' &&
                    menuItems[elem].children.length > 0) {
                    child = menuItems[elem].children[0];

                    if ('' === child.getAttribute('title') ||
                        null === child.getAttribute('title')) {
                        value = child.text.replace(/^\\s+/g, '').split('\\n')[0].replace(/\\s+\$/g, '');
                        child.setAttribute('title', value);
                    }
                }
            }
        }, 25);

    //]]></script>
";
        
        $__internal_43a2d46853ae00ec1b916f44848e13a980228b82e850fcb76fa4551099d97971->leave($__internal_43a2d46853ae00ec1b916f44848e13a980228b82e850fcb76fa4551099d97971_prof);

    }

    // line 33
    public function block_panel($context, array $blocks = array())
    {
        $__internal_98c5ccd7e2be21a9e9f54aa8706fcf5efa62f39e27f43a1ab2c0b6b73da2a56f = $this->env->getExtension("native_profiler");
        $__internal_98c5ccd7e2be21a9e9f54aa8706fcf5efa62f39e27f43a1ab2c0b6b73da2a56f->enter($__internal_98c5ccd7e2be21a9e9f54aa8706fcf5efa62f39e27f43a1ab2c0b6b73da2a56f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        echo "";
        
        $__internal_98c5ccd7e2be21a9e9f54aa8706fcf5efa62f39e27f43a1ab2c0b6b73da2a56f->leave($__internal_98c5ccd7e2be21a9e9f54aa8706fcf5efa62f39e27f43a1ab2c0b6b73da2a56f_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  256 => 33,  177 => 59,  175 => 58,  170 => 57,  158 => 47,  152 => 46,  144 => 43,  136 => 42,  133 => 41,  128 => 40,  124 => 39,  121 => 38,  119 => 37,  114 => 34,  111 => 33,  109 => 32,  105 => 30,  96 => 26,  93 => 25,  87 => 23,  79 => 21,  77 => 20,  73 => 19,  68 => 17,  64 => 16,  61 => 15,  59 => 14,  52 => 9,  50 => 8,  44 => 5,  41 => 4,  35 => 3,  11 => 1,);
    }
}

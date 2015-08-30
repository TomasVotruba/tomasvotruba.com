<?php

/* @Debug/Profiler/dump.html.twig */
class __TwigTemplate_24bb5a37ccf0e3f6e4d247d307a2c637a50c1b0204c6e4c236360dc25b4ae46d extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@Debug/Profiler/dump.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_6c8e21b8655be63ea3565abdfea1f332456da738e0152978de74b7bbd13d59cc = $this->env->getExtension("native_profiler");
        $__internal_6c8e21b8655be63ea3565abdfea1f332456da738e0152978de74b7bbd13d59cc->enter($__internal_6c8e21b8655be63ea3565abdfea1f332456da738e0152978de74b7bbd13d59cc_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Debug/Profiler/dump.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_6c8e21b8655be63ea3565abdfea1f332456da738e0152978de74b7bbd13d59cc->leave($__internal_6c8e21b8655be63ea3565abdfea1f332456da738e0152978de74b7bbd13d59cc_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_2989dff17d3da03b06c618895e377d25c803949f2c385e134c22069182a61602 = $this->env->getExtension("native_profiler");
        $__internal_2989dff17d3da03b06c618895e377d25c803949f2c385e134c22069182a61602->enter($__internal_2989dff17d3da03b06c618895e377d25c803949f2c385e134c22069182a61602_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        $context["dumps_count"] = $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "dumpsCount", array());
        // line 5
        echo "
    ";
        // line 6
        if ((isset($context["dumps_count"]) ? $context["dumps_count"] : $this->getContext($context, "dumps_count"))) {
            // line 7
            echo "        ";
            ob_start();
            // line 8
            echo "            <svg version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\" height=\"24\" viewBox=\"0 0 24 24\" enable-background=\"new 0 0 24 24\" xml:space=\"preserve\">
                <path fill=\"#AAAAAA\" d=\"M12,22.6c-5.8,0-10.5-4.7-10.5-10.5C1.5,6.3,6.2,1.5,12,1.5s10.5,4.7,10.5,10.5C22.5,17.9,17.8,22.6,12,22.6z M12,4.5c-4.2,0-7.5,3.4-7.5,7.5c0,4.2,3.4,7.5,7.5,7.5s7.5-3.4,7.5-7.5C19.5,7.9,16.2,4.5,12,4.5z\"/>
                <path fill=\"#AAAAAA\" d=\"M12,9.1c-0.8,0-1.5-0.7-1.5-1.5v-6c0-0.8,0.7-1.5,1.5-1.5s1.5,0.7,1.5,1.5v6C13.5,8.4,12.8,9.1,12,9.1zM13.5,22.4v-6c0-0.8-0.7-1.5-1.5-1.5s-1.5,0.7-1.5,1.5v6c0,0.8,0.7,1.5,1.5,1.5S13.5,23.2,13.5,22.4z M23.9,12c0-0.8-0.7-1.5-1.5-1.5h-6c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5h6C23.2,13.5,23.9,12.8,23.9,12z M9.1,12c0-0.8-0.7-1.5-1.5-1.5h-6c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5h6C8.4,13.5,9.1,12.8,9.1,12z\"/>
            </svg>
            <span class=\"sf-toolbar-value\">";
            // line 12
            echo twig_escape_filter($this->env, (isset($context["dumps_count"]) ? $context["dumps_count"] : $this->getContext($context, "dumps_count")), "html", null, true);
            echo "</span>
        ";
            $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 14
            echo "
        ";
            // line 15
            ob_start();
            // line 16
            echo "            ";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "getDumps", array(0 => "html"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dump"]) {
                // line 17
                echo "                <div class=\"sf-toolbar-info-piece\">
                    <span>
                    ";
                // line 19
                if ($this->getAttribute($context["dump"], "file", array())) {
                    // line 20
                    echo "                        ";
                    $context["link"] = $this->env->getExtension('code')->getFileLink($this->getAttribute($context["dump"], "file", array()), $this->getAttribute($context["dump"], "line", array()));
                    // line 21
                    echo "                        ";
                    if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
                        // line 22
                        echo "                            <a href=\"";
                        echo twig_escape_filter($this->env, (isset($context["link"]) ? $context["link"] : $this->getContext($context, "link")), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "file", array()), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                        echo "</a>
                        ";
                    } else {
                        // line 24
                        echo "                            <abbr title=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "file", array()), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                        echo "</abbr>
                        ";
                    }
                    // line 26
                    echo "                    ";
                } else {
                    // line 27
                    echo "                        ";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                    echo "
                    ";
                }
                // line 29
                echo "                    </span>
                    <span class=\"sf-toolbar-file-line\">line ";
                // line 30
                echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "line", array()), "html", null, true);
                echo "</span>
                    ";
                // line 31
                echo $this->getAttribute($context["dump"], "data", array());
                echo "
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dump'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "            <img src=\"data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\" onload=\"var h = this.parentNode.innerHTML, rx=/<script>(.*?)<\\/script>/g, s; while (s = rx.exec(h)) {eval(s[1]);};\" />
        ";
            $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 36
            echo "
        ";
            // line 37
            echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => true));
            echo "
    ";
        }
        
        $__internal_2989dff17d3da03b06c618895e377d25c803949f2c385e134c22069182a61602->leave($__internal_2989dff17d3da03b06c618895e377d25c803949f2c385e134c22069182a61602_prof);

    }

    // line 41
    public function block_menu($context, array $blocks = array())
    {
        $__internal_1ca80b91b36c577d8555c14fe772bda512bdd267796826e96ba573671db945d4 = $this->env->getExtension("native_profiler");
        $__internal_1ca80b91b36c577d8555c14fe772bda512bdd267796826e96ba573671db945d4->enter($__internal_1ca80b91b36c577d8555c14fe772bda512bdd267796826e96ba573671db945d4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 42
        echo "    <span class=\"label\">
        <span class=\"icon\">";
        // line 44
        echo "";
        // line 45
        echo "<svg width=\"28\" height=\"28\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 28 28\" enable-background=\"new 0 0 28 28\" xml:space=\"preserve\"><path fill=\"#3F3F3F\" d=\"M28 13h-1.1C26.5 6.6 21.4 1.5 15 1.1V0h-2v1.1C6.6 1.5 1.5 6.6 1.1 13H0v2h1.1C1.5 21.4 6.6 26.5 13 26.9 V28h2v-1.1c6.4-0.5 11.5-5.6 11.9-11.9H28V13z M15 24.9V19h-2v5.9c-5.3-0.5-9.5-4.7-9.9-9.9H9v-2H3.1C3.5 7.7 7.7 3.5 13 3.1V9h2 V3.1c5.3 0.5 9.5 4.7 9.9 9.9H19v2h5.9C24.5 20.3 20.3 24.5 15 24.9z\"/></svg>";
        // line 46
        echo "";
        // line 47
        echo "</span>
        <strong>dump()</strong>
        <span class=\"count\">
            <span>";
        // line 50
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "dumpsCount", array()), "html", null, true);
        echo "</span>
        </span>
    </span>
";
        
        $__internal_1ca80b91b36c577d8555c14fe772bda512bdd267796826e96ba573671db945d4->leave($__internal_1ca80b91b36c577d8555c14fe772bda512bdd267796826e96ba573671db945d4_prof);

    }

    // line 55
    public function block_panel($context, array $blocks = array())
    {
        $__internal_c4748bf9a1fc233e6d0cff196b0c11f51b61470dfb31a8a0c6c38f8dfe0bdf5e = $this->env->getExtension("native_profiler");
        $__internal_c4748bf9a1fc233e6d0cff196b0c11f51b61470dfb31a8a0c6c38f8dfe0bdf5e->enter($__internal_c4748bf9a1fc233e6d0cff196b0c11f51b61470dfb31a8a0c6c38f8dfe0bdf5e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 56
        echo "    <h2>dump()</h2>

    <style>
        li.sf-dump {
            list-style-type: disc;
        }
        .sf-dump ol>li {
            padding: 0;
        }
        .sf-dump a {
            cursor: pointer;
        }
        .sf-dump-compact {
            display: none;
        }
    </style>

    ";
        // line 73
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "dumpsCount", array())) {
            // line 74
            echo "        <ul class=\"alt\">
            ";
            // line 75
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "getDumps", array(0 => "html"), "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["dump"]) {
                // line 76
                echo "            <li class=\"sf-dump sf-reset\">
                in
                ";
                // line 78
                if ($this->getAttribute($context["dump"], "line", array())) {
                    // line 79
                    echo "                    ";
                    $context["link"] = $this->env->getExtension('code')->getFileLink($this->getAttribute($context["dump"], "file", array()), $this->getAttribute($context["dump"], "line", array()));
                    // line 80
                    echo "                    ";
                    if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
                        // line 81
                        echo "                        <a href=\"";
                        echo twig_escape_filter($this->env, (isset($context["link"]) ? $context["link"] : $this->getContext($context, "link")), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "file", array()), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                        echo "</a>
                    ";
                    } else {
                        // line 83
                        echo "                        <abbr title=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "file", array()), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                        echo "</abbr>
                    ";
                    }
                    // line 85
                    echo "                ";
                } else {
                    // line 86
                    echo "                    ";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "name", array()), "html", null, true);
                    echo "
                ";
                }
                // line 88
                echo "                line ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["dump"], "line", array()), "html", null, true);
                echo ":
                <a onclick=\"var s = this.nextElementSibling; if ('sf-dump-compact' == s.className) {this.innerHTML = '&#9660;'; s.className = 'sf-dump-expanded';} else {this.innerHTML = '&#9654;'; s.className = 'sf-dump-compact';}\">&#9654;</a>
                <span class=\"sf-dump-compact\">
                ";
                // line 91
                if ($this->getAttribute($context["dump"], "fileExcerpt", array())) {
                    echo $this->getAttribute($context["dump"], "fileExcerpt", array());
                } else {
                    echo $this->env->getExtension('code')->fileExcerpt($this->getAttribute($context["dump"], "file", array()), $this->getAttribute($context["dump"], "line", array()));
                }
                // line 92
                echo "                </span>

                ";
                // line 94
                echo $this->getAttribute($context["dump"], "data", array());
                echo "
            </li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['dump'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 97
            echo "        </ul>
    ";
        } else {
            // line 99
            echo "        <p>
            <em>No dumped variable</em>
        </p>
    ";
        }
        
        $__internal_c4748bf9a1fc233e6d0cff196b0c11f51b61470dfb31a8a0c6c38f8dfe0bdf5e->leave($__internal_c4748bf9a1fc233e6d0cff196b0c11f51b61470dfb31a8a0c6c38f8dfe0bdf5e_prof);

    }

    public function getTemplateName()
    {
        return "@Debug/Profiler/dump.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  280 => 99,  276 => 97,  267 => 94,  263 => 92,  257 => 91,  250 => 88,  244 => 86,  241 => 85,  233 => 83,  223 => 81,  220 => 80,  217 => 79,  215 => 78,  211 => 76,  207 => 75,  204 => 74,  202 => 73,  183 => 56,  177 => 55,  166 => 50,  161 => 47,  159 => 46,  157 => 45,  155 => 44,  152 => 42,  146 => 41,  136 => 37,  133 => 36,  129 => 34,  120 => 31,  116 => 30,  113 => 29,  107 => 27,  104 => 26,  96 => 24,  86 => 22,  83 => 21,  80 => 20,  78 => 19,  74 => 17,  69 => 16,  67 => 15,  64 => 14,  59 => 12,  53 => 8,  50 => 7,  48 => 6,  45 => 5,  42 => 4,  36 => 3,  11 => 1,);
    }
}

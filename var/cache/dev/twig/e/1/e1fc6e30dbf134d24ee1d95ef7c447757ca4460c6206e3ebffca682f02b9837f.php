<?php

/* @WebProfiler/Collector/request.html.twig */
class __TwigTemplate_e1fc6e30dbf134d24ee1d95ef7c447757ca4460c6206e3ebffca682f02b9837f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/request.html.twig", 1);
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
        $__internal_3d153799427b0bada09deb6efc123dd50f96ff4cdfd3f2526500ce7c360e114e = $this->env->getExtension("native_profiler");
        $__internal_3d153799427b0bada09deb6efc123dd50f96ff4cdfd3f2526500ce7c360e114e->enter($__internal_3d153799427b0bada09deb6efc123dd50f96ff4cdfd3f2526500ce7c360e114e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/request.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_3d153799427b0bada09deb6efc123dd50f96ff4cdfd3f2526500ce7c360e114e->leave($__internal_3d153799427b0bada09deb6efc123dd50f96ff4cdfd3f2526500ce7c360e114e_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_d107bb28071072a0696492da5868cf58be9719617bca9fda5af87e6fee07835b = $this->env->getExtension("native_profiler");
        $__internal_d107bb28071072a0696492da5868cf58be9719617bca9fda5af87e6fee07835b->enter($__internal_d107bb28071072a0696492da5868cf58be9719617bca9fda5af87e6fee07835b_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "

    ";
        // line 6
        ob_start();
        // line 7
        echo "        ";
        if ($this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "controller", array(), "any", false, true), "class", array(), "any", true, true)) {
            // line 8
            echo "            ";
            $context["link"] = $this->env->getExtension('code')->getFileLink($this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "file", array()), $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "line", array()));
            // line 9
            echo "            ";
            if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
                echo "<a href=\"";
                echo twig_escape_filter($this->env, (isset($context["link"]) ? $context["link"] : $this->getContext($context, "link")), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "file", array()), "html", null, true);
                echo "\">";
            } else {
                echo "<span>";
            }
            // line 10
            echo "
                ";
            // line 11
            echo twig_escape_filter($this->env, strip_tags($this->env->getExtension('code')->abbrClass($this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "class", array()))), "html", null, true);
            // line 13
            if ($this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "method", array())) {
                // line 14
                echo "&nbsp;::&nbsp;";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "method", array()), "html", null, true);
            }
            // line 17
            if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
                echo "</a>";
            } else {
                echo "</span>";
            }
            // line 18
            echo "        ";
        } else {
            // line 19
            echo "            <span>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "html", null, true);
            echo "</span>
        ";
        }
        // line 21
        echo "    ";
        $context["request_handler"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 22
        echo "
    ";
        // line 23
        $context["request_status_code_color"] = (((400 > $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "statuscode", array()))) ? ((((200 == $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "statuscode", array()))) ? ("green") : ("yellow"))) : ("red"));
        // line 24
        echo "
    ";
        // line 25
        ob_start();
        // line 26
        echo "        <span class=\"sf-toolbar-status sf-toolbar-status-";
        echo twig_escape_filter($this->env, (isset($context["request_status_code_color"]) ? $context["request_status_code_color"] : $this->getContext($context, "request_status_code_color")), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "statuscode", array()), "html", null, true);
        echo "</span>
        ";
        // line 27
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "route", array())) {
            // line 28
            echo "            <span class=\"sf-toolbar-label\">@</span>
            <span class=\"sf-toolbar-value sf-toolbar-info-piece-additional\">";
            // line 29
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "route", array()), "html", null, true);
            echo "</span>
        ";
        }
        // line 31
        echo "    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 32
        echo "
    ";
        // line 33
        ob_start();
        // line 34
        echo "        ";
        ob_start();
        // line 35
        echo "            <div class=\"sf-toolbar-info-piece\">
                <b>HTTP status</b>
                <span>";
        // line 37
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "statuscode", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "statustext", array()), "html", null, true);
        echo "</span>
            </div>
            <div class=\"sf-toolbar-info-piece\">
                <b>Controller</b>
                <span>";
        // line 41
        echo twig_escape_filter($this->env, (isset($context["request_handler"]) ? $context["request_handler"] : $this->getContext($context, "request_handler")), "html", null, true);
        echo "</span>
            </div>
            ";
        // line 43
        if ($this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "controller", array(), "any", false, true), "class", array(), "any", true, true)) {
            // line 44
            echo "                <div class=\"sf-toolbar-info-piece\">
                    <b>Controller class</b>
                    <span>";
            // line 46
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "controller", array()), "class", array()), "html", null, true);
            echo "</span>
                </div>
            ";
        }
        // line 49
        echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Route name</b>
                <span>";
        // line 51
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "route", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "route", array()), "NONE")) : ("NONE")), "html", null, true);
        echo "</span>
            </div>
            <div class=\"sf-toolbar-info-piece\">
                <b>Has session</b>
                <span>";
        // line 55
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sessionmetadata", array()))) {
            echo "yes";
        } else {
            echo "no";
        }
        echo "</span>
            </div>
        ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        // line 58
        echo "    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 59
        echo "
    ";
        // line 60
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => (isset($context["profiler_url"]) ? $context["profiler_url"] : $this->getContext($context, "profiler_url"))));
        echo "
";
        
        $__internal_d107bb28071072a0696492da5868cf58be9719617bca9fda5af87e6fee07835b->leave($__internal_d107bb28071072a0696492da5868cf58be9719617bca9fda5af87e6fee07835b_prof);

    }

    // line 63
    public function block_menu($context, array $blocks = array())
    {
        $__internal_8a8c7e117e6908b2be24feb315fa63c88052b453ff728a57df716a9c952ef940 = $this->env->getExtension("native_profiler");
        $__internal_8a8c7e117e6908b2be24feb315fa63c88052b453ff728a57df716a9c952ef940->enter($__internal_8a8c7e117e6908b2be24feb315fa63c88052b453ff728a57df716a9c952ef940_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 64
        echo "<span class=\"label\">
    <span class=\"icon\"><svg width=\"38\" height=\"28\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 38 28\" enable-background=\"new 0 0 38 28\" xml:space=\"preserve\"><path fill=\"#3F3F3F\" d=\"M26.7 20.5c0 0.3 0 0.7 0.1 1c-1.1 1-1.6 1.6-1.8 1.8c0 0 0 0.1 0 0.1c1 1.3 1.5 2 1.5 2 c0.1 0 0.4-0.1 1.1-0.4c0.6-0.3 1-0.6 1.3-0.7c0.2 0.1 0.4 0.2 0.5 0.2c0.1 0 0.3 0.1 0.5 0.2c0.1 0.3 0.2 0.8 0.4 1.4 s0.3 1 0.4 1.1c0 0 0.8-0.1 2.5-0.3c0.1 0 0.1 0 0.1-0.1c0.1-0.3 0.2-1.1 0.1-2.5c0.3-0.2 0.6-0.4 0.8-0.6c1.6 0.5 2.5 0.7 2.6 0.5 l1-2.2l0 0c0.1-0.2-0.6-0.7-2.2-1.5c0-0.4 0-0.7-0.1-1c1.1-1 1.6-1.6 1.8-1.8c0 0 0-0.1 0-0.1c-1-1.3-1.4-1.9-1.5-2l-0.1 0 c-0.6 0.2-1.4 0.6-2.2 1.2c-0.2-0.1-0.4-0.2-0.5-0.2c-0.1 0-0.3-0.1-0.5-0.2c-0.1-0.3-0.2-0.8-0.4-1.4c-0.2-0.7-0.3-1-0.4-1.1 c0 0-0.2 0-0.6 0c-0.4 0-0.8 0.1-1.2 0.1C29.4 14 29.2 14 29.2 14c-0.1 0-0.1 0-0.1 0.1c-0.1 0.3-0.2 1.1-0.1 2.5 c-0.3 0.2-0.6 0.4-0.8 0.6c-1.6-0.5-2.5-0.7-2.6-0.5l-1 2.2C24.5 19.1 25.2 19.6 26.7 20.5z M30.3 18.4c0.6-0.2 1.1-0.2 1.7 0 c0.6 0.2 0.9 0.6 1.2 1.2c0.2 0.6 0.2 1.1 0 1.7c-0.2 0.6-0.6 1-1.2 1.2c-0.6 0.2-1.1 0.2-1.7 0c-0.6-0.2-1-0.6-1.2-1.2 c-0.2-0.6-0.2-1.1 0-1.7C29.3 19.1 29.7 18.7 30.3 18.4z\"/><path fill=\"#3F3F3F\" d=\"M25.9 11.1c0-0.1 0-0.3-0.1-0.4c-0.1-0.1-0.2-0.2-0.3-0.2L22.3 10c-0.1-0.5-0.4-1-0.7-1.7 c0.2-0.3 0.5-0.7 0.9-1.2c0.4-0.5 0.7-0.9 0.8-1.1c0.1-0.1 0.1-0.3 0.1-0.4c0-0.4-1-1.4-2.9-3.2c-0.1-0.1-0.3-0.2-0.4-0.2 c-0.2 0-0.3 0-0.4 0.1l-2.4 1.8c-0.6-0.3-1.1-0.5-1.5-0.6l-0.5-3.1c0-0.1-0.1-0.3-0.2-0.4C15.1 0.1 15 0 14.8 0h-3.8 c-0.3 0-0.5 0.2-0.6 0.5c-0.2 0.6-0.3 1.7-0.5 3.1C9.4 3.8 8.9 4 8.4 4.2L6.1 2.4C6 2.3 5.8 2.3 5.7 2.3c-0.3 0-0.8 0.4-1.6 1.2 C3.3 4.3 2.7 4.9 2.4 5.3C2.3 5.4 2.3 5.5 2.3 5.7c0 0.1 0 0.3 0.1 0.4C3.2 7 3.8 7.8 4.2 8.4C4 9 3.7 9.5 3.6 10l-3.1 0.5 c-0.1 0-0.2 0.1-0.3 0.2C0 10.8 0 10.9 0 11.1v3.7c0 0.1 0 0.3 0.1 0.4c0.1 0.1 0.2 0.2 0.3 0.2l3.1 0.5c0.2 0.5 0.4 1.1 0.7 1.7 c-0.2 0.3-0.5 0.7-1 1.2c-0.4 0.5-0.7 0.9-0.8 1.1c-0.1 0.1-0.1 0.3-0.1 0.4c0 0.4 1 1.4 2.9 3.2c0.1 0.1 0.3 0.2 0.4 0.2 c0.2 0 0.3 0 0.4-0.1l2.4-1.8C9 22 9.5 22.2 10 22.3l0.5 3.1c0 0.1 0.1 0.3 0.2 0.4c0.1 0.1 0.3 0.2 0.4 0.2h3.8 c0.3 0 0.5-0.2 0.6-0.5c0.2-0.6 0.3-1.7 0.5-3.1c0.5-0.2 1.1-0.4 1.6-0.6l2.3 1.8c0.1 0.1 0.3 0.1 0.4 0.1c0.3 0 0.8-0.4 1.6-1.2 c0.8-0.8 1.4-1.4 1.7-1.8c0.1-0.1 0.1-0.2 0.1-0.4c0-0.1 0-0.3-0.1-0.4c-0.8-0.9-1.4-1.7-1.8-2.3c0.3-0.6 0.5-1.1 0.6-1.5l3.1-0.5 c0.1 0 0.2-0.1 0.3-0.2c0.1-0.1 0.1-0.3 0.1-0.4L25.9 11.1L25.9 11.1z M16.6 16.6c-1 1-2.2 1.5-3.7 1.5s-2.7-0.5-3.7-1.5 c-1-1-1.5-2.2-1.5-3.7s0.5-2.7 1.5-3.7c1-1 2.2-1.5 3.7-1.5s2.7 0.5 3.7 1.5c1 1 1.5 2.2 1.5 3.7S17.6 15.6 16.6 16.6z\"/><circle fill=\"#3F3F3F\" cx=\"12.9\" cy=\"12.9\" r=\"2.8\"/></svg></span>
    <strong>Request</strong>
</span>
";
        
        $__internal_8a8c7e117e6908b2be24feb315fa63c88052b453ff728a57df716a9c952ef940->leave($__internal_8a8c7e117e6908b2be24feb315fa63c88052b453ff728a57df716a9c952ef940_prof);

    }

    // line 70
    public function block_panel($context, array $blocks = array())
    {
        $__internal_054be0cccbbe2d4a98d12d3fd69e7145655d419d69ec1a57926deff359be6d7a = $this->env->getExtension("native_profiler");
        $__internal_054be0cccbbe2d4a98d12d3fd69e7145655d419d69ec1a57926deff359be6d7a->enter($__internal_054be0cccbbe2d4a98d12d3fd69e7145655d419d69ec1a57926deff359be6d7a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 71
        echo "    <h2>Request GET Parameters</h2>

    ";
        // line 73
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestquery", array()), "all", array()))) {
            // line 74
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 74)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestquery", array())));
            // line 75
            echo "    ";
        } else {
            // line 76
            echo "        <p>
            <em>No GET parameters</em>
        </p>
    ";
        }
        // line 80
        echo "
    <h2>Request POST Parameters</h2>

    ";
        // line 83
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestrequest", array()), "all", array()))) {
            // line 84
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 84)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestrequest", array())));
            // line 85
            echo "    ";
        } else {
            // line 86
            echo "        <p>
            <em>No POST parameters</em>
        </p>
    ";
        }
        // line 90
        echo "
    <h2>Request Attributes</h2>

    ";
        // line 93
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestattributes", array()), "all", array()))) {
            // line 94
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 94)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestattributes", array())));
            // line 95
            echo "    ";
        } else {
            // line 96
            echo "        <p>
            <em>No attributes</em>
        </p>
    ";
        }
        // line 100
        echo "
    <h2>Request Cookies</h2>

    ";
        // line 103
        if (twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestcookies", array()), "all", array()))) {
            // line 104
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 104)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestcookies", array())));
            // line 105
            echo "    ";
        } else {
            // line 106
            echo "        <p>
            <em>No cookies</em>
        </p>
    ";
        }
        // line 110
        echo "
    <h2>Request Headers</h2>

    ";
        // line 113
        $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 113)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestheaders", array())));
        // line 114
        echo "
    <h2>Request Content</h2>

    ";
        // line 117
        if (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "content", array()) == false)) {
            // line 118
            echo "        <p><em>Request content not available (it was retrieved as a resource).</em></p>
    ";
        } elseif ($this->getAttribute(        // line 119
(isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "content", array())) {
            // line 120
            echo "        <pre>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "content", array()), "html", null, true);
            echo "</pre>
    ";
        } else {
            // line 122
            echo "        <p><em>No content</em></p>
    ";
        }
        // line 124
        echo "
    <h2>Request Server Parameters</h2>

    ";
        // line 127
        $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 127)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "requestserver", array())));
        // line 128
        echo "
    <h2>Response Headers</h2>

    ";
        // line 131
        $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 131)->display(array("bag" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "responseheaders", array())));
        // line 132
        echo "
    <h2>Session Metadata</h2>

    ";
        // line 135
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sessionmetadata", array()))) {
            // line 136
            echo "    ";
            $this->loadTemplate("@WebProfiler/Profiler/table.html.twig", "@WebProfiler/Collector/request.html.twig", 136)->display(array("data" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sessionmetadata", array())));
            // line 137
            echo "    ";
        } else {
            // line 138
            echo "    <p>
        <em>No session metadata</em>
    </p>
    ";
        }
        // line 142
        echo "
    <h2>Session Attributes</h2>

    ";
        // line 145
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sessionattributes", array()))) {
            // line 146
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/table.html.twig", "@WebProfiler/Collector/request.html.twig", 146)->display(array("data" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sessionattributes", array())));
            // line 147
            echo "    ";
        } else {
            // line 148
            echo "        <p>
            <em>No session attributes</em>
        </p>
    ";
        }
        // line 152
        echo "
    <h2>Flashes</h2>

    ";
        // line 155
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "flashes", array()))) {
            // line 156
            echo "        ";
            $this->loadTemplate("@WebProfiler/Profiler/table.html.twig", "@WebProfiler/Collector/request.html.twig", 156)->display(array("data" => $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "flashes", array())));
            // line 157
            echo "    ";
        } else {
            // line 158
            echo "        <p>
            <em>No flashes</em>
        </p>
    ";
        }
        // line 162
        echo "
    ";
        // line 163
        if ($this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "parent", array())) {
            // line 164
            echo "        <h2><a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => $this->getAttribute($this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "parent", array()), "token", array()))), "html", null, true);
            echo "\">Parent request: ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "parent", array()), "token", array()), "html", null, true);
            echo "</a></h2>

        ";
            // line 166
            $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 166)->display(array("bag" => $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "parent", array()), "getcollector", array(0 => "request"), "method"), "requestattributes", array())));
            // line 167
            echo "    ";
        }
        // line 168
        echo "
    ";
        // line 169
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "children", array()))) {
            // line 170
            echo "        <h2>Sub requests</h2>

        ";
            // line 172
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["profile"]) ? $context["profile"] : $this->getContext($context, "profile")), "children", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 173
                echo "            <h3><a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => $this->getAttribute($context["child"], "token", array()))), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "token", array()), "html", null, true);
                echo "</a></h3>
            ";
                // line 174
                $this->loadTemplate("@WebProfiler/Profiler/bag.html.twig", "@WebProfiler/Collector/request.html.twig", 174)->display(array("bag" => $this->getAttribute($this->getAttribute($context["child"], "getcollector", array(0 => "request"), "method"), "requestattributes", array())));
                // line 175
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 176
            echo "    ";
        }
        // line 177
        echo "
";
        
        $__internal_054be0cccbbe2d4a98d12d3fd69e7145655d419d69ec1a57926deff359be6d7a->leave($__internal_054be0cccbbe2d4a98d12d3fd69e7145655d419d69ec1a57926deff359be6d7a_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/request.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  445 => 177,  442 => 176,  436 => 175,  434 => 174,  427 => 173,  423 => 172,  419 => 170,  417 => 169,  414 => 168,  411 => 167,  409 => 166,  401 => 164,  399 => 163,  396 => 162,  390 => 158,  387 => 157,  384 => 156,  382 => 155,  377 => 152,  371 => 148,  368 => 147,  365 => 146,  363 => 145,  358 => 142,  352 => 138,  349 => 137,  346 => 136,  344 => 135,  339 => 132,  337 => 131,  332 => 128,  330 => 127,  325 => 124,  321 => 122,  315 => 120,  313 => 119,  310 => 118,  308 => 117,  303 => 114,  301 => 113,  296 => 110,  290 => 106,  287 => 105,  284 => 104,  282 => 103,  277 => 100,  271 => 96,  268 => 95,  265 => 94,  263 => 93,  258 => 90,  252 => 86,  249 => 85,  246 => 84,  244 => 83,  239 => 80,  233 => 76,  230 => 75,  227 => 74,  225 => 73,  221 => 71,  215 => 70,  204 => 64,  198 => 63,  189 => 60,  186 => 59,  183 => 58,  173 => 55,  166 => 51,  162 => 49,  156 => 46,  152 => 44,  150 => 43,  145 => 41,  136 => 37,  132 => 35,  129 => 34,  127 => 33,  124 => 32,  121 => 31,  116 => 29,  113 => 28,  111 => 27,  104 => 26,  102 => 25,  99 => 24,  97 => 23,  94 => 22,  91 => 21,  85 => 19,  82 => 18,  76 => 17,  72 => 14,  70 => 13,  68 => 11,  65 => 10,  54 => 9,  51 => 8,  48 => 7,  46 => 6,  42 => 4,  36 => 3,  11 => 1,);
    }
}

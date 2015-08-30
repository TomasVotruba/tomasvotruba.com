<?php

/* @WebProfiler/Collector/config.html.twig */
class __TwigTemplate_64b8e520bcfcbccc5c87783f3051c9697cafd98ad6a52fa3c29a59a67b4134d1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/config.html.twig", 1);
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
        $__internal_9a05eb79a8b4dd688ea52721dfcf33af5d82a8d2f7f7a497aa79f835d205088f = $this->env->getExtension("native_profiler");
        $__internal_9a05eb79a8b4dd688ea52721dfcf33af5d82a8d2f7f7a497aa79f835d205088f->enter($__internal_9a05eb79a8b4dd688ea52721dfcf33af5d82a8d2f7f7a497aa79f835d205088f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/config.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_9a05eb79a8b4dd688ea52721dfcf33af5d82a8d2f7f7a497aa79f835d205088f->leave($__internal_9a05eb79a8b4dd688ea52721dfcf33af5d82a8d2f7f7a497aa79f835d205088f_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_2febd4455a15328f1e82eecfb135c2eba8248f8ceff952c299929ea82107cda3 = $this->env->getExtension("native_profiler");
        $__internal_2febd4455a15328f1e82eecfb135c2eba8248f8ceff952c299929ea82107cda3->enter($__internal_2febd4455a15328f1e82eecfb135c2eba8248f8ceff952c299929ea82107cda3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        if (("unknown" == $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyState", array()))) {
            // line 5
            echo "        ";
            $context["block_status"] = "";
            // line 6
            echo "        ";
            $context["symfony_version_status"] = "Unable to retrieve information about the Symfony version.";
            // line 7
            echo "    ";
        } elseif (("eol" == $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyState", array()))) {
            // line 8
            echo "        ";
            $context["block_status"] = "red";
            // line 9
            echo "        ";
            $context["symfony_version_status"] = "This Symfony version will no longer receive security fixes.";
            // line 10
            echo "    ";
        } elseif (("eom" == $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyState", array()))) {
            // line 11
            echo "        ";
            $context["block_status"] = "yellow";
            // line 12
            echo "        ";
            $context["symfony_version_status"] = "This Symfony version will only receive security fixes.";
            // line 13
            echo "    ";
        } elseif (("dev" == $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyState", array()))) {
            // line 14
            echo "        ";
            $context["block_status"] = "yellow";
            // line 15
            echo "        ";
            $context["symfony_version_status"] = "This Symfony version is still in the development phase.";
            // line 16
            echo "    ";
        } else {
            // line 17
            echo "        ";
            $context["block_status"] = "";
            // line 18
            echo "        ";
            $context["symfony_version_status"] = "";
            // line 19
            echo "    ";
        }
        // line 20
        echo "
    ";
        // line 21
        ob_start();
        // line 22
        echo "        ";
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "symfonyState", array(), "any", true, true)) {
            // line 23
            echo "            <span class=\"sf-toolbar-label\">
                ";
            // line 24
            echo twig_include($this->env, $context, "@WebProfiler/Icon/symfony.svg");
            echo "
            </span>
            <span class=\"sf-toolbar-value\">";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyversion", array()), "html", null, true);
            echo "</span>
        ";
        } elseif ($this->getAttribute(        // line 27
(isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array())) {
            // line 28
            echo "            <span class=\"sf-toolbar-label\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array()), "html", null, true);
            echo "</span>
            <span class=\"sf-toolbar-value\">";
            // line 29
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationversion", array()), "html", null, true);
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
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array())) {
            // line 35
            echo "            <div class=\"sf-toolbar-info-piece\">
                ";
            // line 36
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array()), "html", null, true);
            echo " <b>";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationversion", array()), "html", null, true);
            echo "</b>
            </div>
        ";
        }
        // line 39
        echo "
        <div class=\"sf-toolbar-info-piece\">
            <b>Profiler token</b>
            <span>
                ";
        // line 43
        if ((isset($context["profiler_url"]) ? $context["profiler_url"] : $this->getContext($context, "profiler_url"))) {
            // line 44
            echo "                    <a href=\"";
            echo twig_escape_filter($this->env, (isset($context["profiler_url"]) ? $context["profiler_url"] : $this->getContext($context, "profiler_url")), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "token", array()), "html", null, true);
            echo "</a>
                ";
        } else {
            // line 46
            echo "                    ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "token", array()), "html", null, true);
            echo "
                ";
        }
        // line 48
        echo "            </span>
        </div>

        ";
        // line 51
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "appname", array()))) {
            // line 52
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Kernel name</b>
                <span>";
            // line 54
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "appname", array()), "html", null, true);
            echo "</span>
            </div>
        ";
        }
        // line 57
        echo "
        ";
        // line 58
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "env", array()))) {
            // line 59
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Environment</b>
                <span>";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "env", array()), "html", null, true);
            echo "</span>
            </div>
        ";
        }
        // line 64
        echo "
        ";
        // line 65
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "debug", array()))) {
            // line 66
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Debug</b>
                <span class=\"";
            // line 68
            echo twig_escape_filter($this->env, (isset($context["debug_status_class"]) ? $context["debug_status_class"] : $this->getContext($context, "debug_status_class")), "html", null, true);
            echo "\">";
            echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "debug", array())) ? ("enabled") : ("disabled"));
            echo "</span>
            </div>
        ";
        }
        // line 71
        echo "
        <div class=\"sf-toolbar-info-piece sf-toolbar-info-php\">
            <b>PHP version</b>
            <span>
                ";
        // line 75
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "phpversion", array()), "html", null, true);
        echo "
                &nbsp; <a href=\"";
        // line 76
        echo $this->env->getExtension('routing')->getPath("_profiler_phpinfo");
        echo "\">View phpinfo()</a>
            </span>
        </div>

        <div class=\"sf-toolbar-info-piece sf-toolbar-info-php-ext\">
            <b>PHP Extensions</b>
            <span class=\"sf-toolbar-status sf-toolbar-status-";
        // line 82
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasxdebug", array())) ? ("green") : ("red"));
        echo "\">xdebug</span>
            <span class=\"sf-toolbar-status sf-toolbar-status-";
        // line 83
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasaccelerator", array())) ? ("green") : ("red"));
        echo "\">accel</span>
        </div>

        <div class=\"sf-toolbar-info-piece\">
            <b>PHP SAPI</b>
            <span>";
        // line 88
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "sapiName", array()), "html", null, true);
        echo "</span>
        </div>

        ";
        // line 91
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "symfonyversion", array(), "any", true, true)) {
            // line 92
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Resources</b>
                <span>
                    <a href=\"https://symfony.com/doc/";
            // line 95
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyversion", array()), "html", null, true);
            echo "/index.html\" rel=\"help\">
                        Read Symfony ";
            // line 96
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyversion", array()), "html", null, true);
            echo " Docs
                    </a>
                </span>
            </div>
        ";
        }
        // line 101
        echo "    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 102
        echo "
    ";
        // line 103
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => false, "status" => (isset($context["block_status"]) ? $context["block_status"] : $this->getContext($context, "block_status"))));
        echo "
";
        
        $__internal_2febd4455a15328f1e82eecfb135c2eba8248f8ceff952c299929ea82107cda3->leave($__internal_2febd4455a15328f1e82eecfb135c2eba8248f8ceff952c299929ea82107cda3_prof);

    }

    // line 106
    public function block_menu($context, array $blocks = array())
    {
        $__internal_a8e712b6d29e3fa22fd3f188f39517953e3df425b50f665512acfd48df993354 = $this->env->getExtension("native_profiler");
        $__internal_a8e712b6d29e3fa22fd3f188f39517953e3df425b50f665512acfd48df993354->enter($__internal_a8e712b6d29e3fa22fd3f188f39517953e3df425b50f665512acfd48df993354_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 107
        echo "<span class=\"label\">
    <span class=\"icon\"><svg width=\"21\" height=\"28\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 21 28\" enable-background=\"new 0 0 21 28\" xml:space=\"preserve\"><g><path fill=\"#3F3F3F\" d=\"M5 17H1c-0.5 0-1-0.4-1-1V9c0-0.5 0.4-1 1-1h4c0.5 0 1 0.4 1 1v7C6 16.6 5.6 17 5 17z\"/><path fill=\"#3F3F3F\" d=\"M19 17h-4c-0.5 0-1-0.4-1-1V9c0-0.5 0.4-1 1-1h4c0.5 0 1 0.4 1 1v7C20 16.6 19.6 17 19 17z\"/><path fill=\"#3F3F3F\" d=\"M12.1 20h-4c-0.5 0-1-0.4-1-1v-7c0-0.5 0.4-1 1-1h4c0.5 0 1 0.4 1 1v7C13.1 19.5 12.6 20 12.1 20z\"/><rect x=\"9.1\" y=\"5.8\" fill=\"#3F3F3F\" width=\"2\" height=\"4.5\"/><rect x=\"16\" y=\"17.7\" fill=\"#3F3F3F\" width=\"2\" height=\"4.5\"/><rect x=\"2.1\" y=\"17.7\" fill=\"#3F3F3F\" width=\"2\" height=\"4.5\"/><rect x=\"9.1\" y=\"20.7\" fill=\"#3F3F3F\" width=\"2\" height=\"1.5\"/><rect x=\"2.1\" y=\"5.7\" fill=\"#3F3F3F\" width=\"2\" height=\"1.5\"/><rect x=\"16.1\" y=\"5.7\" fill=\"#3F3F3F\" width=\"2\" height=\"1.5\"/></g></svg></span>
    <strong>Config</strong>
</span>
";
        
        $__internal_a8e712b6d29e3fa22fd3f188f39517953e3df425b50f665512acfd48df993354->leave($__internal_a8e712b6d29e3fa22fd3f188f39517953e3df425b50f665512acfd48df993354_prof);

    }

    // line 113
    public function block_panel($context, array $blocks = array())
    {
        $__internal_c6c677576f252114145e2d1848483dee369708e2e70b0cf105d3e78fbf53da0a = $this->env->getExtension("native_profiler");
        $__internal_c6c677576f252114145e2d1848483dee369708e2e70b0cf105d3e78fbf53da0a->enter($__internal_c6c677576f252114145e2d1848483dee369708e2e70b0cf105d3e78fbf53da0a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 114
        echo "    <h2>Project Configuration</h2>
    <table>
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        <tr>
            ";
        // line 121
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array())) {
            // line 122
            echo "                <th>Application</th>
                <td>";
            // line 123
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationname", array()), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "applicationversion", array()), "html", null, true);
            echo " (on Symfony ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyversion", array()), "html", null, true);
            echo ")</td>
            ";
        } else {
            // line 125
            echo "                <th>Symfony version</th>
                <td>";
            // line 126
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "symfonyversion", array()), "html", null, true);
            echo "</td>
            ";
        }
        // line 128
        echo "        </tr>
        ";
        // line 129
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "appname", array()))) {
            // line 130
            echo "            <tr>
                <th>Application name</th>
                <td>";
            // line 132
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "appname", array()), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 135
        echo "        ";
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "env", array()))) {
            // line 136
            echo "            <tr>
                <th>Environment</th>
                <td>";
            // line 138
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "env", array()), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 141
        echo "        ";
        if (("n/a" != $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "debug", array()))) {
            // line 142
            echo "            <tr>
                <th>Debug</th>
                <td>";
            // line 144
            echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "debug", array())) ? ("enabled") : ("disabled"));
            echo "</td>
            </tr>
        ";
        }
        // line 147
        echo "    </table>

    <h2>PHP configuration</h2>
    <table>
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        <tr>
            <th>PHP version</th>
            <td>";
        // line 157
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "phpversion", array()), "html", null, true);
        echo "</td>
        </tr>
        <tr>
            <th>Xdebug</th>
            <td>";
        // line 161
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasxdebug", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>PHP acceleration</th>
            <td>";
        // line 165
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasaccelerator", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>XCache</th>
            <td>";
        // line 169
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasxcache", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>APC</th>
            <td>";
        // line 173
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasapc", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>Zend OPcache</th>
            <td>";
        // line 177
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "haszendopcache", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>EAccelerator</th>
            <td>";
        // line 181
        echo (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "haseaccelerator", array())) ? ("enabled") : ("disabled"));
        echo "</td>
        </tr>
        <tr>
            <th>Full PHP configuration</th>
            <td><a href=\"";
        // line 185
        echo $this->env->getExtension('routing')->getPath("_profiler_phpinfo");
        echo "\"><code>phpinfo</code></a></td>
        </tr>
    </table>

    ";
        // line 189
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "bundles", array())) {
            // line 190
            echo "        <h2>Active bundles</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Path</th>
            </tr>
            ";
            // line 196
            $context["bundles"] = $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "bundles", array());
            // line 197
            echo "            ";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable(twig_sort_filter(twig_get_array_keys_filter((isset($context["bundles"]) ? $context["bundles"] : $this->getContext($context, "bundles")))));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 198
                echo "            <tr>
                <th>";
                // line 199
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</th>
                <td>";
                // line 200
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["bundles"]) ? $context["bundles"] : $this->getContext($context, "bundles")), $context["name"], array(), "array"), "html", null, true);
                echo "</td>
            </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 203
            echo "        </table>
    ";
        }
        
        $__internal_c6c677576f252114145e2d1848483dee369708e2e70b0cf105d3e78fbf53da0a->leave($__internal_c6c677576f252114145e2d1848483dee369708e2e70b0cf105d3e78fbf53da0a_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/config.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  482 => 203,  473 => 200,  469 => 199,  466 => 198,  461 => 197,  459 => 196,  451 => 190,  449 => 189,  442 => 185,  435 => 181,  428 => 177,  421 => 173,  414 => 169,  407 => 165,  400 => 161,  393 => 157,  381 => 147,  375 => 144,  371 => 142,  368 => 141,  362 => 138,  358 => 136,  355 => 135,  349 => 132,  345 => 130,  343 => 129,  340 => 128,  335 => 126,  332 => 125,  323 => 123,  320 => 122,  318 => 121,  309 => 114,  303 => 113,  292 => 107,  286 => 106,  277 => 103,  274 => 102,  271 => 101,  263 => 96,  259 => 95,  254 => 92,  252 => 91,  246 => 88,  238 => 83,  234 => 82,  225 => 76,  221 => 75,  215 => 71,  207 => 68,  203 => 66,  201 => 65,  198 => 64,  192 => 61,  188 => 59,  186 => 58,  183 => 57,  177 => 54,  173 => 52,  171 => 51,  166 => 48,  160 => 46,  152 => 44,  150 => 43,  144 => 39,  136 => 36,  133 => 35,  130 => 34,  128 => 33,  125 => 32,  122 => 31,  117 => 29,  112 => 28,  110 => 27,  106 => 26,  101 => 24,  98 => 23,  95 => 22,  93 => 21,  90 => 20,  87 => 19,  84 => 18,  81 => 17,  78 => 16,  75 => 15,  72 => 14,  69 => 13,  66 => 12,  63 => 11,  60 => 10,  57 => 9,  54 => 8,  51 => 7,  48 => 6,  45 => 5,  42 => 4,  36 => 3,  11 => 1,);
    }
}

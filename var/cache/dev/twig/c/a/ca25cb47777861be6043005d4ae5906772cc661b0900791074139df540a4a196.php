<?php

/* @Security/Collector/security.html.twig */
class __TwigTemplate_ca25cb47777861be6043005d4ae5906772cc661b0900791074139df540a4a196 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@Security/Collector/security.html.twig", 1);
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
        $__internal_32c0a60995e15a5ba915f7d9ea1cffc09c67888b1fca78dfbf279fa09dffe404 = $this->env->getExtension("native_profiler");
        $__internal_32c0a60995e15a5ba915f7d9ea1cffc09c67888b1fca78dfbf279fa09dffe404->enter($__internal_32c0a60995e15a5ba915f7d9ea1cffc09c67888b1fca78dfbf279fa09dffe404_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Security/Collector/security.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_32c0a60995e15a5ba915f7d9ea1cffc09c67888b1fca78dfbf279fa09dffe404->leave($__internal_32c0a60995e15a5ba915f7d9ea1cffc09c67888b1fca78dfbf279fa09dffe404_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_2d3dce920a7ecf5fbf513b065d5b31bcf4cb6f961e901fa19b84345f99e06f28 = $this->env->getExtension("native_profiler");
        $__internal_2d3dce920a7ecf5fbf513b065d5b31bcf4cb6f961e901fa19b84345f99e06f28->enter($__internal_2d3dce920a7ecf5fbf513b065d5b31bcf4cb6f961e901fa19b84345f99e06f28_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array())) {
            // line 5
            echo "        ";
            $context["is_authenticated"] = ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "enabled", array()) && $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "authenticated", array()));
            // line 6
            echo "        ";
            $context["color_code"] = (((isset($context["is_authenticated"]) ? $context["is_authenticated"] : $this->getContext($context, "is_authenticated"))) ? ("") : ("yellow"));
            // line 7
            echo "    ";
        } else {
            // line 8
            echo "        ";
            $context["color_code"] = (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "enabled", array())) ? ("red") : (""));
            // line 9
            echo "    ";
        }
        // line 10
        echo "
    ";
        // line 11
        ob_start();
        // line 12
        echo "        <svg version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\" height=\"24\" viewBox=\"0 0 24 24\" enable-background=\"new 0 0 24 24\" xml:space=\"preserve\">
            <path fill=\"#AAAAAA\" d=\"M21,20.4V22H3v-1.6c0-3.7,2.4-6.9,5.8-8c-1.7-1.1-2.9-3-2.9-5.2c0-3.4,2.7-6.1,6.1-6.1s6.1,2.7,6.1,6.1c0,2.2-1.2,4.1-2.9,5.2C18.6,13.5,21,16.7,21,20.4z\"/>
        </svg>
        <span class=\"sf-toolbar-value\">";
        // line 15
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "user", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["collector"]) ? $context["collector"] : null), "user", array()), "n/a")) : ("n/a")), "html", null, true);
        echo "</span>
    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 17
        echo "
    ";
        // line 18
        ob_start();
        // line 19
        echo "        ";
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array())) {
            // line 20
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Logged in as</b>
                <span>";
            // line 22
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "user", array()), "html", null, true);
            echo "</span>
            </div>
            <div class=\"sf-toolbar-info-piece\">
                <b>Authenticated</b>
                <span class=\"sf-toolbar-status sf-toolbar-status-";
            // line 26
            echo (((isset($context["is_authenticated"]) ? $context["is_authenticated"] : $this->getContext($context, "is_authenticated"))) ? ("green") : ("red"));
            echo "\">";
            echo (((isset($context["is_authenticated"]) ? $context["is_authenticated"] : $this->getContext($context, "is_authenticated"))) ? ("Yes") : ("No"));
            echo "</span>
            </div>
            ";
            // line 28
            if (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array()) != null)) {
                // line 29
                echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Token class</b>
                <span>";
                // line 31
                echo $this->env->getExtension('code')->abbrClass($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array()));
                echo "</span>
            </div>
            ";
            }
            // line 34
            echo "        ";
        } elseif ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "enabled", array())) {
            // line 35
            echo "            <div class=\"sf-toolbar-info-piece\">
                <span>You are not authenticated.</span>
            </div>
        ";
        } else {
            // line 39
            echo "            <div class=\"sf-toolbar-info-piece\">
                <span>The security is disabled.</span>
            </div>
        ";
        }
        // line 43
        echo "    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 44
        echo "
    ";
        // line 45
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => (isset($context["profiler_url"]) ? $context["profiler_url"] : $this->getContext($context, "profiler_url")), "status" => (isset($context["color_code"]) ? $context["color_code"] : $this->getContext($context, "color_code"))));
        echo "
";
        
        $__internal_2d3dce920a7ecf5fbf513b065d5b31bcf4cb6f961e901fa19b84345f99e06f28->leave($__internal_2d3dce920a7ecf5fbf513b065d5b31bcf4cb6f961e901fa19b84345f99e06f28_prof);

    }

    // line 48
    public function block_menu($context, array $blocks = array())
    {
        $__internal_193291da360ae37c27425a4f1ff3ef6e2b9117d9801ce52898c9d70ac5b83ad8 = $this->env->getExtension("native_profiler");
        $__internal_193291da360ae37c27425a4f1ff3ef6e2b9117d9801ce52898c9d70ac5b83ad8->enter($__internal_193291da360ae37c27425a4f1ff3ef6e2b9117d9801ce52898c9d70ac5b83ad8_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 49
        echo "<span class=\"label\">
    <span class=\"icon\"><svg width=\"42\" height=\"30\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 42 30\" enable-background=\"new 0 0 42 30\" xml:space=\"preserve\"><g><path fill=\"#3F3F3F\" d=\"M16.1 15.3c0-0.5 0-0.8 0-1.2c0.2-0.1 0.6-0.8 0.7-1.4c0.2 0 0.4-0.2 0.5-0.8c0-0.4-0.1-0.5-0.2-0.6 c0.3-0.8 0.8-3.4-1.1-3.7C15.7 7.2 15.2 7 14.6 7c-2.6 0-2.9 2-2.4 4.2c-0.1 0.1-0.3 0.3-0.2 0.6c0.1 0.7 0.3 0.8 0.5 0.8 c0.1 0.6 0.5 1.3 0.7 1.4c0 0.4 0 0.8 0 1.2c-0.5 1.4-4 1-4.2 3.7h11.2C20.1 16.3 16.6 16.7 16.1 15.3z\"/><path fill=\"#3F3F3F\" d=\"M28.3 10.2c-0.2 0-0.5 0.1-0.7 0.2c-0.2 0.1-0.4 0.3-0.6 0.6c-0.2 0.3-0.4 0.6-0.5 1s-0.2 0.7-0.2 1.1 c0 0.5 0.1 0.9 0.4 1.2s0.5 0.4 0.8 0.4c0.2 0 0.4-0.1 0.7-0.2c0.2-0.1 0.5-0.3 0.7-0.6s0.4-0.6 0.5-1c0.1-0.4 0.2-0.8 0.2-1.2 c0-0.5-0.1-0.9-0.4-1.2C29 10.3 28.7 10.2 28.3 10.2z\"/><path fill=\"#3F3F3F\" d=\"M34 4H7C6.4 4 6 4.4 6 5v19c0 0.6 0.4 1 1 1h5v-1c-0.6 0-1-0.4-1-1c0-0.6 0.4-1 1-1h2c0.6 0 1 0.4 1 1 c0 0.6-0.4 1-1 1v1h13v-1c-0.6 0-1-0.4-1-1c0-0.6 0.4-1 1-1h2c0.6 0 1 0.4 1 1c0 0.6-0.4 1-1 1v1h5c0.6 0 1-0.4 1-1V5 C35 4.4 34.6 4 34 4z M21 19c0 0.6-0.4 1-1 1H9c-0.6 0-1-0.4-1-1V7c0-0.6 0.4-1 1-1h11c0.6 0 1 0.4 1 1V19z M32.9 16.6 c-0.5 0.4-1 0.7-1.7 1c-0.6 0.2-1.4 0.4-2.3 0.4c-0.8 0-1.6-0.1-2.3-0.3c-0.7-0.2-1.3-0.5-1.8-1c-0.5-0.4-0.9-0.9-1.1-1.5 c-0.3-0.7-0.5-1.5-0.5-2.3c0-0.9 0.2-1.8 0.6-2.6c0.5-1 1.1-1.8 2-2.4C26.5 7.3 27.6 7 28.8 7c0.9 0 1.8 0.2 2.5 0.6 c0.7 0.4 1.3 1 1.8 1.7c0.4 0.7 0.6 1.4 0.6 2.1c0 1.1-0.4 2.1-1.2 2.9c-0.7 0.8-1.4 1.2-2.3 1.2c-0.3 0-0.5 0-0.6-0.1 c-0.2-0.1-0.3-0.2-0.4-0.3c0-0.1-0.1-0.3-0.1-0.5c-0.2 0.3-0.5 0.5-0.8 0.7c-0.3 0.2-0.6 0.3-0.9 0.3c-0.3 0-0.7-0.1-1-0.3 c-0.3-0.2-0.6-0.5-0.8-0.9c-0.2-0.4-0.3-0.9-0.3-1.3c0-0.6 0.2-1.2 0.5-1.8c0.3-0.6 0.7-1.1 1.2-1.4c0.5-0.3 0.9-0.5 1.3-0.5 c0.3 0 0.6 0.1 0.9 0.3c0.3 0.2 0.6 0.4 0.8 0.8l0.2-0.9h1l-0.8 3.8c-0.1 0.5-0.2 0.8-0.2 0.9c0 0.1 0 0.2 0.1 0.3 c0.1 0.1 0.2 0.1 0.3 0.1c0.2 0 0.5-0.1 0.8-0.3c0.4-0.3 0.8-0.7 1-1.2c0.3-0.5 0.4-1 0.4-1.6c0-0.6-0.2-1.2-0.5-1.8 c-0.3-0.6-0.8-1-1.5-1.3s-1.4-0.5-2.1-0.5c-0.9 0-1.7 0.2-2.4 0.6c-0.7 0.4-1.3 1-1.7 1.8C24.2 11 24 11.9 24 12.7 c0 0.9 0.2 1.7 0.6 2.4c0.4 0.7 1 1.2 1.8 1.5c0.8 0.3 1.6 0.5 2.5 0.5c1 0 1.8-0.2 2.5-0.5c0.7-0.3 1.2-0.7 1.5-1.2h1 C33.7 15.8 33.4 16.2 32.9 16.6z\"/></g></svg></span>
    <strong>Security</strong>
</span>
";
        
        $__internal_193291da360ae37c27425a4f1ff3ef6e2b9117d9801ce52898c9d70ac5b83ad8->leave($__internal_193291da360ae37c27425a4f1ff3ef6e2b9117d9801ce52898c9d70ac5b83ad8_prof);

    }

    // line 55
    public function block_panel($context, array $blocks = array())
    {
        $__internal_1ba682597d2006376b3878bd3f704b1721f1cb451be9a924f8e2c21237cebc8f = $this->env->getExtension("native_profiler");
        $__internal_1ba682597d2006376b3878bd3f704b1721f1cb451be9a924f8e2c21237cebc8f->enter($__internal_1ba682597d2006376b3878bd3f704b1721f1cb451be9a924f8e2c21237cebc8f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 56
        echo "    <h2>Security</h2>
    ";
        // line 57
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array())) {
            // line 58
            echo "        <table>
            <tr>
                <th>Username</th>
                <td>";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "user", array()), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <th>Authenticated?</th>
                <td>
                    ";
            // line 66
            if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "authenticated", array())) {
                // line 67
                echo "                        yes
                    ";
            } else {
                // line 69
                echo "                        no ";
                if ( !twig_length_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "roles", array()))) {
                    echo "<em>(probably because the user has no roles)</em>";
                }
                // line 70
                echo "                    ";
            }
            // line 71
            echo "                </td>
            </tr>
            <tr>
                <th>Roles</th>
                <td>";
            // line 75
            echo twig_escape_filter($this->env, $this->env->getExtension('yaml')->encode($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "roles", array())), "html", null, true);
            echo "</td>
            </tr>
            ";
            // line 77
            if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "supportsRoleHierarchy", array())) {
                // line 78
                echo "            <tr>
                <th>Inherited Roles</th>
                <td>";
                // line 80
                echo twig_escape_filter($this->env, $this->env->getExtension('yaml')->encode($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "inheritedRoles", array())), "html", null, true);
                echo "</td>
            </tr>
            ";
            }
            // line 83
            echo "            ";
            if (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array()) != null)) {
                // line 84
                echo "            <tr>
                <th>Token class</th>
                <td>";
                // line 86
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "tokenClass", array()), "html", null, true);
                echo "</td>
            </tr>
            ";
            }
            // line 89
            echo "        </table>
    ";
        } elseif ($this->getAttribute(        // line 90
(isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "enabled", array())) {
            // line 91
            echo "        <p>
            <em>No token</em>
        </p>
    ";
        } else {
            // line 95
            echo "        <p>
            <em>The security component is disabled</em>
        </p>
    ";
        }
        
        $__internal_1ba682597d2006376b3878bd3f704b1721f1cb451be9a924f8e2c21237cebc8f->leave($__internal_1ba682597d2006376b3878bd3f704b1721f1cb451be9a924f8e2c21237cebc8f_prof);

    }

    public function getTemplateName()
    {
        return "@Security/Collector/security.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  245 => 95,  239 => 91,  237 => 90,  234 => 89,  228 => 86,  224 => 84,  221 => 83,  215 => 80,  211 => 78,  209 => 77,  204 => 75,  198 => 71,  195 => 70,  190 => 69,  186 => 67,  184 => 66,  176 => 61,  171 => 58,  169 => 57,  166 => 56,  160 => 55,  149 => 49,  143 => 48,  134 => 45,  131 => 44,  128 => 43,  122 => 39,  116 => 35,  113 => 34,  107 => 31,  103 => 29,  101 => 28,  94 => 26,  87 => 22,  83 => 20,  80 => 19,  78 => 18,  75 => 17,  70 => 15,  65 => 12,  63 => 11,  60 => 10,  57 => 9,  54 => 8,  51 => 7,  48 => 6,  45 => 5,  42 => 4,  36 => 3,  11 => 1,);
    }
}

<?php

/* @WebProfiler/Collector/twig.html.twig */
class __TwigTemplate_b537a8a1332cd4996bbf9f4b8d1bca329dc2a47ca98bf6bd594e974b90dbcb68 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/twig.html.twig", 1);
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
        $__internal_5dc578e6f417734c58cd19a5ca87042cf235772daeaed799430b85b36d9f0f25 = $this->env->getExtension("native_profiler");
        $__internal_5dc578e6f417734c58cd19a5ca87042cf235772daeaed799430b85b36d9f0f25->enter($__internal_5dc578e6f417734c58cd19a5ca87042cf235772daeaed799430b85b36d9f0f25_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/twig.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5dc578e6f417734c58cd19a5ca87042cf235772daeaed799430b85b36d9f0f25->leave($__internal_5dc578e6f417734c58cd19a5ca87042cf235772daeaed799430b85b36d9f0f25_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_68521cfddba6dd8c69effde89a92fa47fa57b509f8ebe587579b9f9f766268d7 = $this->env->getExtension("native_profiler");
        $__internal_68521cfddba6dd8c69effde89a92fa47fa57b509f8ebe587579b9f9f766268d7->enter($__internal_68521cfddba6dd8c69effde89a92fa47fa57b509f8ebe587579b9f9f766268d7_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        $context["time"] = (($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templatecount", array())) ? (sprintf("%0.0f", $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "time", array()))) : ("n/a"));
        // line 5
        echo "    ";
        ob_start();
        // line 6
        echo "        ";
        echo twig_include($this->env, $context, "@WebProfiler/Icon/twig.svg");
        echo "
        <span class=\"sf-toolbar-value\">";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["time"]) ? $context["time"] : $this->getContext($context, "time")), "html", null, true);
        echo "</span>
        <span class=\"sf-toolbar-label\">ms</span>
    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 10
        echo "
    ";
        // line 11
        ob_start();
        // line 12
        echo "        <div class=\"sf-toolbar-info-piece\">
            <b>Render Time</b>
            <span>";
        // line 14
        echo twig_escape_filter($this->env, (isset($context["time"]) ? $context["time"] : $this->getContext($context, "time")), "html", null, true);
        echo " ms</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Template Calls</b>
            <span class=\"sf-toolbar-status\">";
        // line 18
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templatecount", array()), "html", null, true);
        echo "</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Block Calls</b>
            <span class=\"sf-toolbar-status\">";
        // line 22
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "blockcount", array()), "html", null, true);
        echo "</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Macro Calls</b>
            <span class=\"sf-toolbar-status\">";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "macrocount", array()), "html", null, true);
        echo "</span>
        </div>
    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 29
        echo "
    ";
        // line 30
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => (isset($context["profiler_url"]) ? $context["profiler_url"] : $this->getContext($context, "profiler_url"))));
        echo "
";
        
        $__internal_68521cfddba6dd8c69effde89a92fa47fa57b509f8ebe587579b9f9f766268d7->leave($__internal_68521cfddba6dd8c69effde89a92fa47fa57b509f8ebe587579b9f9f766268d7_prof);

    }

    // line 33
    public function block_menu($context, array $blocks = array())
    {
        $__internal_bbad355cf9b9dc15204bf568a7fcfeaec6ce5e4c5d6eca847365e6fdfacd6432 = $this->env->getExtension("native_profiler");
        $__internal_bbad355cf9b9dc15204bf568a7fcfeaec6ce5e4c5d6eca847365e6fdfacd6432->enter($__internal_bbad355cf9b9dc15204bf568a7fcfeaec6ce5e4c5d6eca847365e6fdfacd6432_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 34
        echo "<span class=\"label\">
    <span class=\"icon\"><img alt=\"Twig\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAgCAYAAAABtRhCAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAbklEQVRIx2NgGAVDHTDCGGlpaf9pZcmsWbPg9rAgS6SlpdHCMhQ+E72DlAWbYHFJCcUG9/b0YBWnuw9HLaRPosEV4cPHh9iyBczXxGaZ0WxBfBwwM4/mw1ELRy0c4MK7s7NzCPsQvYU1CkYBNgAAV5UW+fU+ZL4AAAAASUVORK5CYII=\"></span>
    <strong>Twig</strong>
    <span class=\"count\">
        <span>";
        // line 38
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templatecount", array()), "html", null, true);
        echo "</span>
        <span>";
        // line 39
        echo twig_escape_filter($this->env, sprintf("%0.0f ms", $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "time", array())), "html", null, true);
        echo "</span>
    </span>
</span>
";
        
        $__internal_bbad355cf9b9dc15204bf568a7fcfeaec6ce5e4c5d6eca847365e6fdfacd6432->leave($__internal_bbad355cf9b9dc15204bf568a7fcfeaec6ce5e4c5d6eca847365e6fdfacd6432_prof);

    }

    // line 44
    public function block_panel($context, array $blocks = array())
    {
        $__internal_42a8b9e849a593bf6152977c4f562e606052c66ba86ea0053233437daa0cb6c3 = $this->env->getExtension("native_profiler");
        $__internal_42a8b9e849a593bf6152977c4f562e606052c66ba86ea0053233437daa0cb6c3->enter($__internal_42a8b9e849a593bf6152977c4f562e606052c66ba86ea0053233437daa0cb6c3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 45
        echo "    ";
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templatecount", array())) {
            // line 46
            echo "        <h2>Twig Stats</h2>

        <table>
            <tr>
                <th>Total Render Time<br /><small>including sub-requests rendering time</small></th>
                <td><pre>";
            // line 51
            echo twig_escape_filter($this->env, sprintf("%0.0f ms", $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "time", array())), "html", null, true);
            echo "</pre></td>
            </tr>
            <tr>
                <th scope=\"col\" style=\"width: 30%\">Template Calls</th>
                <td scope=\"col\" style=\"width: 60%\"><pre>";
            // line 55
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templatecount", array()), "html", null, true);
            echo "</pre></td>
            </tr>
            <tr>
                <th>Block Calls</th>
                <td><pre>";
            // line 59
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "blockcount", array()), "html", null, true);
            echo "</pre></td>
            </tr>
            <tr>
                <th>Macro Calls</th>
                <td><pre>";
            // line 63
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "macrocount", array()), "html", null, true);
            echo "</pre></td>
            </tr>
        </table>

        <h2>Rendered Templates</h2>

        <table>
            <tr>
                <th scope=\"col\">Template Name</th>
                <th scope=\"col\">Render Count</th>
            </tr>
            ";
            // line 74
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "templates", array()));
            foreach ($context['_seq'] as $context["template"] => $context["count"]) {
                // line 75
                echo "                <tr>
                    <td><code>";
                // line 76
                echo twig_escape_filter($this->env, $context["template"], "html", null, true);
                echo "</code></td>
                    <td><pre>";
                // line 77
                echo twig_escape_filter($this->env, $context["count"], "html", null, true);
                echo "</pre></td>
                </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['template'], $context['count'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 80
            echo "        </table>

        <h2>Rendering Call Graph</h2>

        ";
            // line 84
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "htmlcallgraph", array()), "html", null, true);
            echo "
    ";
        } else {
            // line 86
            echo "        <p><em>No Twig templates were rendered for this request.</em></p>
    ";
        }
        
        $__internal_42a8b9e849a593bf6152977c4f562e606052c66ba86ea0053233437daa0cb6c3->leave($__internal_42a8b9e849a593bf6152977c4f562e606052c66ba86ea0053233437daa0cb6c3_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/twig.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  216 => 86,  211 => 84,  205 => 80,  196 => 77,  192 => 76,  189 => 75,  185 => 74,  171 => 63,  164 => 59,  157 => 55,  150 => 51,  143 => 46,  140 => 45,  134 => 44,  123 => 39,  119 => 38,  113 => 34,  107 => 33,  98 => 30,  95 => 29,  89 => 26,  82 => 22,  75 => 18,  68 => 14,  64 => 12,  62 => 11,  59 => 10,  53 => 7,  48 => 6,  45 => 5,  42 => 4,  36 => 3,  11 => 1,);
    }
}

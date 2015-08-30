<?php

/* @WebProfiler/Profiler/toolbar_item.html.twig */
class __TwigTemplate_83496cda19127b30751f6473f1ec09733b380958c3663f2f39d58d9304477bb3 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_866dbc9a8d7709f3cd81be22053b9b5c9c09bfac0423ead80a8a2416f34a29a0 = $this->env->getExtension("native_profiler");
        $__internal_866dbc9a8d7709f3cd81be22053b9b5c9c09bfac0423ead80a8a2416f34a29a0->enter($__internal_866dbc9a8d7709f3cd81be22053b9b5c9c09bfac0423ead80a8a2416f34a29a0_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/toolbar_item.html.twig"));

        // line 1
        echo "<div class=\"sf-toolbar-block sf-toolbar-block-";
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : $this->getContext($context, "name")), "html", null, true);
        echo " sf-toolbar-status-";
        echo twig_escape_filter($this->env, ((array_key_exists("status", $context)) ? (_twig_default_filter((isset($context["status"]) ? $context["status"] : $this->getContext($context, "status")), "normal")) : ("normal")), "html", null, true);
        echo "\">
    ";
        // line 2
        if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "panel" => (isset($context["name"]) ? $context["name"] : $this->getContext($context, "name")))), "html", null, true);
            echo "\">";
        }
        // line 3
        echo "        <div class=\"sf-toolbar-icon\">";
        echo twig_escape_filter($this->env, ((array_key_exists("icon", $context)) ? (_twig_default_filter((isset($context["icon"]) ? $context["icon"] : $this->getContext($context, "icon")), "")) : ("")), "html", null, true);
        echo "</div>
    ";
        // line 4
        if ((isset($context["link"]) ? $context["link"] : $this->getContext($context, "link"))) {
            echo "</a>";
        }
        // line 5
        echo "        <div class=\"sf-toolbar-info\">";
        echo twig_escape_filter($this->env, ((array_key_exists("text", $context)) ? (_twig_default_filter((isset($context["text"]) ? $context["text"] : $this->getContext($context, "text")), "")) : ("")), "html", null, true);
        echo "</div>
</div>
";
        
        $__internal_866dbc9a8d7709f3cd81be22053b9b5c9c09bfac0423ead80a8a2416f34a29a0->leave($__internal_866dbc9a8d7709f3cd81be22053b9b5c9c09bfac0423ead80a8a2416f34a29a0_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/toolbar_item.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 5,  40 => 4,  35 => 3,  29 => 2,  22 => 1,);
    }
}

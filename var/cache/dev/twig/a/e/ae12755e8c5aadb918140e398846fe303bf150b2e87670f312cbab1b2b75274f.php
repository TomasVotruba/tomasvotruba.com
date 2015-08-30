<?php

/* TwigBundle:Exception:exception_full.html.twig */
class __TwigTemplate_ae12755e8c5aadb918140e398846fe303bf150b2e87670f312cbab1b2b75274f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("TwigBundle::layout.html.twig", "TwigBundle:Exception:exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "TwigBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_d046101af4ffc558da03f64966972d4871559910e666f7068d751b0a5a2a14a3 = $this->env->getExtension("native_profiler");
        $__internal_d046101af4ffc558da03f64966972d4871559910e666f7068d751b0a5a2a14a3->enter($__internal_d046101af4ffc558da03f64966972d4871559910e666f7068d751b0a5a2a14a3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "TwigBundle:Exception:exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_d046101af4ffc558da03f64966972d4871559910e666f7068d751b0a5a2a14a3->leave($__internal_d046101af4ffc558da03f64966972d4871559910e666f7068d751b0a5a2a14a3_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_e3e7869b810341dc68968e210a9edb8c625040c32444c90929b5f7fe388c145c = $this->env->getExtension("native_profiler");
        $__internal_e3e7869b810341dc68968e210a9edb8c625040c32444c90929b5f7fe388c145c->enter($__internal_e3e7869b810341dc68968e210a9edb8c625040c32444c90929b5f7fe388c145c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('request')->generateAbsoluteUrl($this->env->getExtension('asset')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_e3e7869b810341dc68968e210a9edb8c625040c32444c90929b5f7fe388c145c->leave($__internal_e3e7869b810341dc68968e210a9edb8c625040c32444c90929b5f7fe388c145c_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_91f3050b00d841f0fe649dc8c0c5b373ff1f40c9c826c6d1ad6ae12022048c70 = $this->env->getExtension("native_profiler");
        $__internal_91f3050b00d841f0fe649dc8c0c5b373ff1f40c9c826c6d1ad6ae12022048c70->enter($__internal_91f3050b00d841f0fe649dc8c0c5b373ff1f40c9c826c6d1ad6ae12022048c70_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_91f3050b00d841f0fe649dc8c0c5b373ff1f40c9c826c6d1ad6ae12022048c70->leave($__internal_91f3050b00d841f0fe649dc8c0c5b373ff1f40c9c826c6d1ad6ae12022048c70_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_0b0941f17d44f93eeb920190e7092dffac03720c9e3bc1c6d7532567dc7dd726 = $this->env->getExtension("native_profiler");
        $__internal_0b0941f17d44f93eeb920190e7092dffac03720c9e3bc1c6d7532567dc7dd726->enter($__internal_0b0941f17d44f93eeb920190e7092dffac03720c9e3bc1c6d7532567dc7dd726_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("TwigBundle:Exception:exception.html.twig", "TwigBundle:Exception:exception_full.html.twig", 12)->display($context);
        
        $__internal_0b0941f17d44f93eeb920190e7092dffac03720c9e3bc1c6d7532567dc7dd726->leave($__internal_0b0941f17d44f93eeb920190e7092dffac03720c9e3bc1c6d7532567dc7dd726_prof);

    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }
}

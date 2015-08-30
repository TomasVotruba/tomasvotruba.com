<?php

/* @BlogDomainBundle/blog/index.html.twig */
class __TwigTemplate_2a9d2b1303e3562fc474821baa03e821eac4865e2657dec220fb0a77c609b386 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "@BlogDomainBundle/blog/index.html.twig", 1);
        $this->blocks = array(
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_88949081ad19e415e8a07153a6ff5ca23b7610ab2a8dc3f9b4d1e6b69bf1c7a6 = $this->env->getExtension("native_profiler");
        $__internal_88949081ad19e415e8a07153a6ff5ca23b7610ab2a8dc3f9b4d1e6b69bf1c7a6->enter($__internal_88949081ad19e415e8a07153a6ff5ca23b7610ab2a8dc3f9b4d1e6b69bf1c7a6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@BlogDomainBundle/blog/index.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_88949081ad19e415e8a07153a6ff5ca23b7610ab2a8dc3f9b4d1e6b69bf1c7a6->leave($__internal_88949081ad19e415e8a07153a6ff5ca23b7610ab2a8dc3f9b4d1e6b69bf1c7a6_prof);

    }

    // line 3
    public function block_main($context, array $blocks = array())
    {
        $__internal_a9b95dad681c7d2b903a2c7a4669c0f62a7d01af0f7e1429e8e84db01a43a2a4 = $this->env->getExtension("native_profiler");
        $__internal_a9b95dad681c7d2b903a2c7a4669c0f62a7d01af0f7e1429e8e84db01a43a2a4->enter($__internal_a9b95dad681c7d2b903a2c7a4669c0f62a7d01af0f7e1429e8e84db01a43a2a4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "main"));

        // line 4
        echo "    <h2>Příspěvky!</h2>

";
        
        $__internal_a9b95dad681c7d2b903a2c7a4669c0f62a7d01af0f7e1429e8e84db01a43a2a4->leave($__internal_a9b95dad681c7d2b903a2c7a4669c0f62a7d01af0f7e1429e8e84db01a43a2a4_prof);

    }

    public function getTemplateName()
    {
        return "@BlogDomainBundle/blog/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 4,  34 => 3,  11 => 1,);
    }
}

<?php

/* @WebProfiler/Collector/ajax.html.twig */
class __TwigTemplate_063cabf5c0b5e507b4d09dfa8dbac1c4612f4928e6f2093ba5c60b08342b500b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/ajax.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_863d19d4e639be1b8e7bce8f9955b1cf6ab39a4e5ac46244b46e19ad3b928cb5 = $this->env->getExtension("native_profiler");
        $__internal_863d19d4e639be1b8e7bce8f9955b1cf6ab39a4e5ac46244b46e19ad3b928cb5->enter($__internal_863d19d4e639be1b8e7bce8f9955b1cf6ab39a4e5ac46244b46e19ad3b928cb5_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/ajax.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_863d19d4e639be1b8e7bce8f9955b1cf6ab39a4e5ac46244b46e19ad3b928cb5->leave($__internal_863d19d4e639be1b8e7bce8f9955b1cf6ab39a4e5ac46244b46e19ad3b928cb5_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_ed269fb96613f61f30c8eeec552c35a8cab1f80ce7cd9e52aa72a3c662710d9e = $this->env->getExtension("native_profiler");
        $__internal_ed269fb96613f61f30c8eeec552c35a8cab1f80ce7cd9e52aa72a3c662710d9e->enter($__internal_ed269fb96613f61f30c8eeec552c35a8cab1f80ce7cd9e52aa72a3c662710d9e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        ob_start();
        // line 5
        echo "        ";
        echo twig_include($this->env, $context, "@WebProfiler/Icon/ajax.svg");
        echo "
        <span class=\"sf-toolbar-value sf-toolbar-ajax-requests\">0</span>
    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 8
        echo "
    ";
        // line 9
        $context["text"] = ('' === $tmp = "        <div class=\"sf-toolbar-info-piece\">
            <b class=\"sf-toolbar-ajax-info\"></b>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <table class=\"sf-toolbar-ajax-requests\">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>URL</th>
                        <th>Time</th>
                        <th>Profile</th>
                    </tr>
                </thead>
                <tbody class=\"sf-toolbar-ajax-request-list\"></tbody>
            </table>
        </div>
    ") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 27
        echo "
    ";
        // line 28
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => false));
        echo "
";
        
        $__internal_ed269fb96613f61f30c8eeec552c35a8cab1f80ce7cd9e52aa72a3c662710d9e->leave($__internal_ed269fb96613f61f30c8eeec552c35a8cab1f80ce7cd9e52aa72a3c662710d9e_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/ajax.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 28,  71 => 27,  53 => 9,  50 => 8,  43 => 5,  40 => 4,  34 => 3,  11 => 1,);
    }
}

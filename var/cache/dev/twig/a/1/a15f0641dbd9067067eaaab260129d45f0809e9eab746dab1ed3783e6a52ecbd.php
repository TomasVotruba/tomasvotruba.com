<?php

/* default/homepage.html.twig */
class __TwigTemplate_a15f0641dbd9067067eaaab260129d45f0809e9eab746dab1ed3783e6a52ecbd extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "default/homepage.html.twig", 1);
        $this->blocks = array(
            'body_id' => array($this, 'block_body_id'),
            'header' => array($this, 'block_header'),
            'footer' => array($this, 'block_footer'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_3be5955e43d1cbb3868add6dee58f8b14156bda113fa8c2838db53217e9fb2d2 = $this->env->getExtension("native_profiler");
        $__internal_3be5955e43d1cbb3868add6dee58f8b14156bda113fa8c2838db53217e9fb2d2->enter($__internal_3be5955e43d1cbb3868add6dee58f8b14156bda113fa8c2838db53217e9fb2d2_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "default/homepage.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_3be5955e43d1cbb3868add6dee58f8b14156bda113fa8c2838db53217e9fb2d2->leave($__internal_3be5955e43d1cbb3868add6dee58f8b14156bda113fa8c2838db53217e9fb2d2_prof);

    }

    // line 3
    public function block_body_id($context, array $blocks = array())
    {
        $__internal_084b75a5ecd416bce181d8fc984a3a28d3ef7f1e92f29b4ec749f1b333e46488 = $this->env->getExtension("native_profiler");
        $__internal_084b75a5ecd416bce181d8fc984a3a28d3ef7f1e92f29b4ec749f1b333e46488->enter($__internal_084b75a5ecd416bce181d8fc984a3a28d3ef7f1e92f29b4ec749f1b333e46488_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body_id"));

        echo "homepage";
        
        $__internal_084b75a5ecd416bce181d8fc984a3a28d3ef7f1e92f29b4ec749f1b333e46488->leave($__internal_084b75a5ecd416bce181d8fc984a3a28d3ef7f1e92f29b4ec749f1b333e46488_prof);

    }

    // line 9
    public function block_header($context, array $blocks = array())
    {
        $__internal_28c95a33db9b01ff5e7ab85ddbf30f1061d5254b9c9676a8e60f673122d06fff = $this->env->getExtension("native_profiler");
        $__internal_28c95a33db9b01ff5e7ab85ddbf30f1061d5254b9c9676a8e60f673122d06fff->enter($__internal_28c95a33db9b01ff5e7ab85ddbf30f1061d5254b9c9676a8e60f673122d06fff_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "header"));

        
        $__internal_28c95a33db9b01ff5e7ab85ddbf30f1061d5254b9c9676a8e60f673122d06fff->leave($__internal_28c95a33db9b01ff5e7ab85ddbf30f1061d5254b9c9676a8e60f673122d06fff_prof);

    }

    // line 10
    public function block_footer($context, array $blocks = array())
    {
        $__internal_07be8492bb1db173397e4188b74283954d9e1173b8b7210e3502c8bc4fbea77f = $this->env->getExtension("native_profiler");
        $__internal_07be8492bb1db173397e4188b74283954d9e1173b8b7210e3502c8bc4fbea77f->enter($__internal_07be8492bb1db173397e4188b74283954d9e1173b8b7210e3502c8bc4fbea77f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "footer"));

        
        $__internal_07be8492bb1db173397e4188b74283954d9e1173b8b7210e3502c8bc4fbea77f->leave($__internal_07be8492bb1db173397e4188b74283954d9e1173b8b7210e3502c8bc4fbea77f_prof);

    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        $__internal_9a6f2f63630cc3ba4bcafd842da7605aef1d7493c2d53f92b0d395c9ac4f092e = $this->env->getExtension("native_profiler");
        $__internal_9a6f2f63630cc3ba4bcafd842da7605aef1d7493c2d53f92b0d395c9ac4f092e->enter($__internal_9a6f2f63630cc3ba4bcafd842da7605aef1d7493c2d53f92b0d395c9ac4f092e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 13
        echo "    <div class=\"page-header\">
        <h1>Tomáš Votruba<strong>.cz</strong></h1>
    </div>

    <div class=\"row\">
        <div class=\"col-sm-4\">
            <div class=\"jumbotron\">
                Zaplním Vaše mezery ve znalostech Nette a Symfony
            </div>
        </div>
        <div class=\"col-sm-4\">
            <div class=\"jumbotron\">
                Spisuji čistý SOLID kód
            </div>
        </div>
        <div class=\"col-sm-4\">
            <div class=\"jumbotron\">
                Ladím Symfony komunitu
            </div>
        </div>
    </div>

    <hr>

    <a href=\"";
        // line 37
        echo $this->env->getExtension('routing')->getPath("blog_index");
        echo "\">Blog</a>

    <hr>

    Twitter...

    Github...


";
        
        $__internal_9a6f2f63630cc3ba4bcafd842da7605aef1d7493c2d53f92b0d395c9ac4f092e->leave($__internal_9a6f2f63630cc3ba4bcafd842da7605aef1d7493c2d53f92b0d395c9ac4f092e_prof);

    }

    public function getTemplateName()
    {
        return "default/homepage.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  103 => 37,  77 => 13,  71 => 12,  60 => 10,  49 => 9,  37 => 3,  11 => 1,);
    }
}

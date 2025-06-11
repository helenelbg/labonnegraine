<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig */
class __TwigTemplate_3548652664478005580133fcbb21d13f620ec9cb48237f81bf06157f0a68f59e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig"));

        // line 25
        echo "<div class=\"modal fade\" id=\"edit-specific-price-modal\" tabindex=\"1\" role=\"dialog\" aria-hidden=\"true\">

  <div class=\"modal-dialog\" role=\"document\">
    <div class=\"modal-content\">

      <div class=\"modal-header\">
        <h5 class=\"modal-title\">
          ";
        // line 32
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Edit a specific price", [], "Admin.Catalog.Feature"), "html", null, true);
        echo "
        </h5>
        <button type=\"button\" class=\"close\" id=\"form_modal_close\" aria-label=\"Close\">
          <span aria-hidden=\"true\">&times;</span>
        </button>
      </div>

      <div id=\"edit-specific-price-modal-form\" class=\"modal-body\" data-action=\"";
        // line 39
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_specific_price_update");
        echo "\" data-specific-price-id=\"0\">
      </div>
    </div>
  </div>

</div>
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 39,  52 => 32,  43 => 25,);
    }

    public function getSourceContext()
    {
        return new Source("{#**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *#}
<div class=\"modal fade\" id=\"edit-specific-price-modal\" tabindex=\"1\" role=\"dialog\" aria-hidden=\"true\">

  <div class=\"modal-dialog\" role=\"document\">
    <div class=\"modal-content\">

      <div class=\"modal-header\">
        <h5 class=\"modal-title\">
          {{ \"Edit a specific price\"|trans({}, 'Admin.Catalog.Feature') }}
        </h5>
        <button type=\"button\" class=\"close\" id=\"form_modal_close\" aria-label=\"Close\">
          <span aria-hidden=\"true\">&times;</span>
        </button>
      </div>

      <div id=\"edit-specific-price-modal-form\" class=\"modal-body\" data-action=\"{{ path('admin_specific_price_update') }}\" data-specific-price-id=\"0\">
      </div>
    </div>
  </div>

</div>
", "@Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig", "/home/dev.labonnegraine.com/public_html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig");
    }
}

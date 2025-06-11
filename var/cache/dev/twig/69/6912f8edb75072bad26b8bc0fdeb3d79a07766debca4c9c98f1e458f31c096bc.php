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

/* __string_template__73ecd62107498bcd0e80e8a2f8a1b7d6b4551b5e7cc6059d7dd53493f3b00269 */
class __TwigTemplate_868c7af68aedb0df5883e6d0e0816537f3cb78f410eefc8c8af228dbf575baf6 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'stylesheets' => [$this, 'block_stylesheets'],
            'extra_stylesheets' => [$this, 'block_extra_stylesheets'],
            'content_header' => [$this, 'block_content_header'],
            'content' => [$this, 'block_content'],
            'content_footer' => [$this, 'block_content_footer'],
            'sidebar_right' => [$this, 'block_sidebar_right'],
            'javascripts' => [$this, 'block_javascripts'],
            'extra_javascripts' => [$this, 'block_extra_javascripts'],
            'translate_javascripts' => [$this, 'block_translate_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "__string_template__73ecd62107498bcd0e80e8a2f8a1b7d6b4551b5e7cc6059d7dd53493f3b00269"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "__string_template__73ecd62107498bcd0e80e8a2f8a1b7d6b4551b5e7cc6059d7dd53493f3b00269"));

        // line 1
        echo "<!DOCTYPE html>
<html lang=\"fr\">
<head>
  <meta charset=\"utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
<meta name=\"robots\" content=\"NOFOLLOW, NOINDEX\">

<link rel=\"icon\" type=\"image/x-icon\" href=\"/img/favicon.ico\" />
<link rel=\"apple-touch-icon\" href=\"/img/app_icon.png\" />

<title>Maintenance • La Bonne Graine</title>

  <script type=\"text/javascript\">
    var help_class_name = 'AdminMaintenance';
    var iso_user = 'fr';
    var lang_is_rtl = '0';
    var full_language_code = 'fr';
    var full_cldr_language_code = 'fr-FR';
    var country_iso_code = 'FR';
    var _PS_VERSION_ = '8.0.1';
    var roundMode = 2;
    var youEditFieldFor = '';
        var new_order_msg = 'Une nouvelle commande a été passée sur votre boutique.';
    var order_number_msg = 'Numéro de commande : ';
    var total_msg = 'Total : ';
    var from_msg = 'Du ';
    var see_order_msg = 'Afficher cette commande';
    var new_customer_msg = 'Un nouveau client s\\'est inscrit sur votre boutique';
    var customer_name_msg = 'Nom du client : ';
    var new_msg = 'Un nouveau message a été posté sur votre boutique.';
    var see_msg = 'Lire le message';
    var token = 'c0c92e1f879658cfb998234f752e7a25';
    var token_admin_orders = tokenAdminOrders = '2dae91902180567ff334d3da06e9e6c3';
    var token_admin_customers = '60299276d5706a56cfcb320d277af8de';
    var token_admin_customer_threads = tokenAdminCustomerThreads = 'd4480d95d5e21e52437a51288ee1b582';
    var currentIndex = 'index.php?controller=AdminMaintenance';
    var employee_token = '121823b38070a62abc4d0c663acc4a02';
    var choose_language_translate = 'Choisissez la langue :';
    var default_language = '1';
    var admin_modules_link = 'https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage';
    var admin_notification_get_link = 'https://dev.labonnegraine.com/admin123/index.php/common/notifications';
    var admin_notification_";
        // line 43
        echo "push_link = adminNotificationPushLink = 'https://dev.labonnegraine.com/admin123/index.php/common/notifications/ack';
    var tab_modules_list = '';
    var update_success_msg = 'Mise à jour réussie';
    var search_product_msg = 'Rechercher un produit';
  </script>



<link
      rel=\"preload\"
      href=\"/admin123/themes/new-theme/public/auto703cf8f274fbb265d49c6262825780e1.preload.woff2\"
      as=\"font\"
      crossorigin
    >
      <link href=\"/admin123/themes/new-theme/public/theme.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/chosen/jquery.chosen.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/fancybox/jquery.fancybox.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/blockwishlist/public/backoffice.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/admin123/themes/default/css/vendor/nv.d3.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/creativeelements/views/css/admin-ce.css?v=2.11.0\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/psaffiliate/views/css/back.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ets_affiliatemarketing/views/css/admin_all.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ybc_blog/views/css/admin_all.css\" rel=\"stylesheet\" type=\"text/css\"/>
  
  <script type=\"text/javascript\">
var baseAdminDir = \"\\/admin123\\/\";
var baseDir = \"\\/\";
var ceAdmin = {\"uid\":\"0020001\",\"hideEditor\":[],\"footerProduct\":\"\",\"i18n\":{\"edit\":\"Edit with Creative Elements\",\"save\":\"Veuillez enregistrer le formulaire avant de le modifier avec Creative Elements.\"},\"editorUrl\":\"https:\\/\\/dev.labonnegraine.com\\/admin123\\/index.php?controller=AdminCEEditor&amp;uid=\",\"languages\":[{\"id_lang\":\"1\",\"name\":\"Fran\\u00e7ais (French)\",\"active\":\"1\",\"iso_code\":\"fr\",\"language_code\":\"fr\",\"locale\":\"fr-FR\",\"date_format_lite\":\"d\\/m\\/Y\",\"date_format_full\":\"d\\/m\\/Y H:i:s\",\"is_rtl\":\"0\",\"id_shop\":\"1\",\"shops\":{\"1\":true}}],\"editSuppliers\":0,\"edit";
        // line 70
        echo "Manufacturers\":0};
var changeFormLanguageUrl = \"https:\\/\\/dev.labonnegraine.com\\/admin123\\/index.php\\/configure\\/advanced\\/employees\\/change-form-language\";
var currency = {\"iso_code\":\"EUR\",\"sign\":\"\\u20ac\",\"name\":\"Euro\",\"format\":null};
var currency_specifications = {\"symbol\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"currencyCode\":\"EUR\",\"currencySymbol\":\"\\u20ac\",\"numberSymbols\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.00\\u00a0\\u00a4\",\"negativePattern\":\"-#,##0.00\\u00a0\\u00a4\",\"maxFractionDigits\":2,\"minFractionDigits\":2,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var number_specifications = {\"symbol\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"numberSymbols\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.###\",\"negativePattern\":\"-#,##0.###\",\"maxFractionDigits\":3,\"minFractionDigits\":0,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var prestashop = {\"debug\":true};
var show_new_customers = \"\";
var show_new_messages = \"\";
var show_new_orders = \"\";
</script>
<script type=\"text/javascript\" src=\"/admin123/themes/new-theme/public/main.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/jquery.chosen.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/fancybox/jquery.fancybox.js\"></script>
<script type=\"text/javascript\" src=\"/js/admin.js?v=8.0.1\"></script>
<script type=\"text/javascript\" src=\"/admin123/themes/new-theme/public/cldr.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/tools.js?v=8.0.1\"></script>
<script type=\"text/javascript\" src=\"/modules/blockwishlist/public/vendors.js\"></script>
<script type=\"text/javascript\" src=\"/js/vendor/d3.v3.min.js\"></script>
<script type=\"text/javascript\" src=\"/admin123/themes/default/js/vendor/nv.d3.min.js\"></script>
<script type=\"text/javascript\" src=\"/modules/creativeelements/views/js/admin-ce.js?v=2";
        // line 89
        echo ".11.0\"></script>
<script type=\"text/javascript\" src=\"/modules/ets_affiliatemarketing/views/js/admin_all.js\"></script>
<script type=\"text/javascript\" src=\"/modules/aw_mailsmailjet/views/js/aw_mailsmailjet_back_office.js\"></script>
<script type=\"text/javascript\" src=\"/modules/aw_impressionadresse/aw_impressionadresse.js?v=6\"></script>
<script type=\"text/javascript\" src=\"/modules/awproduct/views/js/custom.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ybc_blog/views/js/admin_all.js\"></script>

  <style>
i.mi-ce {
\tfont-size: 14px !important;
}
i.icon-AdminParentCEContent, i.mi-ce {
\tposition: relative;
\theight: 1em;
\twidth: 1.2857em;
}
i.icon-AdminParentCEContent:before, i.mi-ce:before,
i.icon-AdminParentCEContent:after, i.mi-ce:after {
\tcontent: '';
\tposition: absolute;
\tmargin: 0;
\tleft: .2143em;
\ttop: 0;
\twidth: .9286em;
\theight: .6428em;
\tborder-width: .2143em 0;
\tborder-style: solid;
\tborder-color: currentColor;
\tbox-sizing: content-box;
}
i.icon-AdminParentCEContent:after, i.mi-ce:after {
\ttop: .4286em;
\twidth: .6428em;
\theight: 0;
\tborder-width: .2143em 0 0;
}
#maintab-AdminParentCreativeElements, #subtab-AdminParentCreativeElements {
\tdisplay: none;
}
</style>
<script type=\"text/html\" id=\"tmpl-btn-back-to-ps\">
    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEEditor&amp;action=backToPsEditor\" class=\"btn btn-default btn-back-to-ps\"><i class=\"material-icons\">navigate_before</i> Revenir à l’éditeur PrestaShop</a>
</script>
<script type=\"text/html\" id=\"tmpl-btn-edit-with-ce\">
    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEEditor\" class=\"btn pointer btn-edit-with-ce\"><i class=\"material-icons mi-ce\"></i> Modifier avec Creative Elements</a>
</script>
<script type=\"application/javascript\">
                        \$(document).ready(function() {
                            \$.get(\"/modules/dpdfrance/cron.php?token=df0f15ff0991cb6ed8a899c3f9207206&employee=17\");
                        });
     ";
        // line 139
        echo "               </script>

";
        // line 141
        $this->displayBlock('stylesheets', $context, $blocks);
        $this->displayBlock('extra_stylesheets', $context, $blocks);
        echo "</head>";
        echo "

<body
  class=\"lang-fr adminmaintenance\"
  data-base-url=\"/admin123/index.php\"  data-token=\"h5q5Vo6aGP6PuYPemGXjALLHLGbXHsJk5GilpTp8VAQ\">

  <header id=\"header\" class=\"d-print-none\">

    <nav id=\"header_infos\" class=\"main-header\">
      <button class=\"btn btn-primary-reverse onclick btn-lg unbind ajax-spinner\"></button>

            <i class=\"material-icons js-mobile-menu\">menu</i>
      <a id=\"header_logo\" class=\"logo float-left\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\"></a>
      <span id=\"shop_version\">8.0.1</span>

      <div class=\"component\" id=\"quick-access-container\">
        <div class=\"dropdown quick-accesses\">
  <button class=\"btn btn-link btn-sm dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" id=\"quick_select\">
    Accès rapide
  </button>
  <div class=\"dropdown-menu\">
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost&amp;token=fd0d13e8606c3aa0c326c11992702f8c\"
         target=\"_blank\"         data-item=\"Blog\"
      >Blog</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders?token=4abc6762ed44956188f556510f9a700f\"
                 data-item=\"Commandes\"
      >Commandes</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/../../controle.php?token=4abc6762ed44956188f556510f9a700f\"
         target=\"_blank\"         data-item=\"Contrôle Code Barre\"
      >Contrôle Code Barre</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statsstocksinventaire&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
                 data-item=\"Etat des stocks / Inventaire\"
      >Etat des stocks / Inventaire</a>
          <a class=\"dropdown-item quick-row-link\"
    ";
        // line 179
        echo "     href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statscheckup&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
                 data-item=\"Évaluation du catalogue\"
      >Évaluation du catalogue</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ets_megamenu&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Mega Menu\"
      >Mega Menu</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=moduleperso&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Module Perso\"
      >Module Perso</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage?token=4abc6762ed44956188f556510f9a700f\"
                 data-item=\"Modules installés\"
      >Modules installés</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules&amp;addcart_rule&amp;token=f8f9e6419a666825ad443054d4da45ab\"
                 data-item=\"Nouveau bon de réduction\"
      >Nouveau bon de réduction</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=homecategories&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Produits phares\"
      >Produits phares</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ps_imageslider&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"SLIDER\"
      >SLIDER</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?control";
        // line 207
        echo "ler=AdminFlashSales&amp;token=28209f1f2d55b6c1fc5f31f26c7e9f95\"
                 data-item=\"Ventes Flash\"
      >Ventes Flash</a>
        <div class=\"dropdown-divider\"></div>
          <a id=\"quick-add-link\"
        class=\"dropdown-item js-quick-link\"
        href=\"#\"
        data-rand=\"8\"
        data-icon=\"icon-AdminParentPreferences\"
        data-method=\"add\"
        data-url=\"index.php/configure/shop/maintenance\"
        data-post-link=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\"
        data-prompt-text=\"Veuillez nommer ce raccourci :\"
        data-link=\"Maintenance - Liste\"
      >
        <i class=\"material-icons\">add_circle</i>
        Ajouter la page actuelle à l'accès rapide
      </a>
        <a id=\"quick-manage-link\" class=\"dropdown-item\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\">
      <i class=\"material-icons\">settings</i>
      Gérez vos accès rapides
    </a>
  </div>
</div>
      </div>
      <div class=\"component component-search\" id=\"header-search-container\">
        <div class=\"component-search-body\">
          <div class=\"component-search-top\">
            <form id=\"header_search\"
      class=\"bo_search_form dropdown-form js-dropdown-form collapsed\"
      method=\"post\"
      action=\"/admin123/index.php?controller=AdminSearch&amp;token=118f2de0383c2d76d0c8bc6aff8d6cb3\"
      role=\"search\">
  <input type=\"hidden\" name=\"bo_search_type\" id=\"bo_search_type\" class=\"js-search-type\" />
    <div class=\"input-group\">
    <input type=\"text\" class=\"form-control js-form-search\" id=\"bo_query\" name=\"bo_query\" value=\"\" placeholder=\"Rechercher (ex. : référence produit, nom du client, etc.)\" aria-label=\"Barre de recherche\">
    <div class=\"input-group-append\">
      <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle js-dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
        Partout
      </button>
      <div class=\"dropdown";
        // line 247
        echo "-menu js-items-list\">
        <a class=\"dropdown-item\" data-item=\"Partout\" href=\"#\" data-value=\"0\" data-placeholder=\"Que souhaitez-vous trouver ?\" data-icon=\"icon-search\"><i class=\"material-icons\">search</i> Partout</a>
        <div class=\"dropdown-divider\"></div>
        <a class=\"dropdown-item\" data-item=\"Catalogue\" href=\"#\" data-value=\"1\" data-placeholder=\"Nom du produit, référence, etc.\" data-icon=\"icon-book\"><i class=\"material-icons\">store_mall_directory</i> Catalogue</a>
        <a class=\"dropdown-item\" data-item=\"Clients par nom\" href=\"#\" data-value=\"2\" data-placeholder=\"Nom\" data-icon=\"icon-group\"><i class=\"material-icons\">group</i> Clients par nom</a>
        <a class=\"dropdown-item\" data-item=\"Clients par adresse IP\" href=\"#\" data-value=\"6\" data-placeholder=\"123.45.67.89\" data-icon=\"icon-desktop\"><i class=\"material-icons\">desktop_mac</i> Clients par adresse IP</a>
        <a class=\"dropdown-item\" data-item=\"Commandes\" href=\"#\" data-value=\"3\" data-placeholder=\"ID commande\" data-icon=\"icon-credit-card\"><i class=\"material-icons\">shopping_basket</i> Commandes</a>
        <a class=\"dropdown-item\" data-item=\"Factures\" href=\"#\" data-value=\"4\" data-placeholder=\"Numéro de facture\" data-icon=\"icon-book\"><i class=\"material-icons\">book</i> Factures</a>
        <a class=\"dropdown-item\" data-item=\"Paniers\" href=\"#\" data-value=\"5\" data-placeholder=\"ID panier\" data-icon=\"icon-shopping-cart\"><i class=\"material-icons\">shopping_cart</i> Paniers</a>
        <a class=\"dropdown-item\" data-item=\"Modules\" href=\"#\" data-value=\"7\" data-placeholder=\"Nom du module\" data-icon=\"icon-puzzle-piece\"><i class=\"material-icons\">extension</i> Modules</a>
      </div>
      <button class=\"btn btn-primary\" type=\"submit\"><span class=\"d-none\">RECHERCHE</span><i class=\"material-icons\">search</i></button>
    </div>
  </div>
</form>

<script type=\"text/javascript\">
 \$(document).ready(function(){
    \$('#bo_query').one('click', function() {
    \$(this).closest('form').removeClass('collapsed');
  ";
        // line 267
        echo "});
});
</script>
            <button class=\"component-search-cancel d-none\">Annuler</button>
          </div>

          <div class=\"component-search-quickaccess d-none\">
  <p class=\"component-search-title\">Accès rapide</p>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost&amp;token=fd0d13e8606c3aa0c326c11992702f8c\"
       target=\"_blank\"       data-item=\"Blog\"
    >Blog</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders?token=4abc6762ed44956188f556510f9a700f\"
             data-item=\"Commandes\"
    >Commandes</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/../../controle.php?token=4abc6762ed44956188f556510f9a700f\"
       target=\"_blank\"       data-item=\"Contrôle Code Barre\"
    >Contrôle Code Barre</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statsstocksinventaire&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
             data-item=\"Etat des stocks / Inventaire\"
    >Etat des stocks / Inventaire</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statscheckup&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
             data-item=\"Évaluation du catalogue\"
    >Évaluation du catalogue</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ets_megamenu&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"Mega Menu\"
    >Mega Menu</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=moduleperso&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-ite";
        // line 301
        echo "m=\"Module Perso\"
    >Module Perso</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage?token=4abc6762ed44956188f556510f9a700f\"
             data-item=\"Modules installés\"
    >Modules installés</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules&amp;addcart_rule&amp;token=f8f9e6419a666825ad443054d4da45ab\"
             data-item=\"Nouveau bon de réduction\"
    >Nouveau bon de réduction</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=homecategories&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"Produits phares\"
    >Produits phares</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ps_imageslider&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"SLIDER\"
    >SLIDER</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminFlashSales&amp;token=28209f1f2d55b6c1fc5f31f26c7e9f95\"
             data-item=\"Ventes Flash\"
    >Ventes Flash</a>
    <div class=\"dropdown-divider\"></div>
      <a id=\"quick-add-link\"
      class=\"dropdown-item js-quick-link\"
      href=\"#\"
      data-rand=\"66\"
      data-icon=\"icon-AdminParentPreferences\"
      data-method=\"add\"
      data-url=\"index.php/configure/shop/maintenance\"
      data-post-link=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\"
      data-prompt-text=\"Veuillez nommer ce raccourci :\"
      data-link=\"Maintenance - Liste\"
    >
      <i class=\"material-icons\">add_circle</i>
      Ajouter la page actuelle à l'accès rapide
    </a>
    <a id=\"quick-manage-link\" class=\"dropdown-item\" href=\"https://dev.labonnegraine.com/admin1";
        // line 338
        echo "23/index.php?controller=AdminQuickAccesses\">
    <i class=\"material-icons\">settings</i>
    Gérez vos accès rapides
  </a>
</div>
        </div>

        <div class=\"component-search-background d-none\"></div>
      </div>

              <div class=\"component hide-mobile-sm\" id=\"header-debug-mode-container\">
          <a class=\"link shop-state\"
             id=\"debug-mode\"
             data-toggle=\"pstooltip\"
             data-placement=\"bottom\"
             data-html=\"true\"
             title=\"<p class=&quot;text-left&quot;><strong>Votre boutique est en mode debug.</strong></p><p class=&quot;text-left&quot;>Tous les messages et erreurs PHP sont affichés. Lorsque vous n&#039;en avez plus besoin, &lt;strong&gt;désactivez&lt;/strong&gt; ce mode.</p>\"
             href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/performance/\"
          >
            <i class=\"material-icons\">bug_report</i>
            <span>Mode debug</span>
          </a>
        </div>
      
              <div class=\"component hide-mobile-sm\" id=\"header-maintenance-mode-container\">
          <a class=\"link shop-state\"
             id=\"maintenance-mode\"
             data-toggle=\"pstooltip\"
             data-placement=\"bottom\"
             data-html=\"true\"
             title=\"<p class=&quot;text-left&quot;><strong>Votre boutique est en maintenance.</strong></p><p class=&quot;text-left&quot;>Vos clients et visiteurs ne peuvent y accéder actuellement. &amp;lt;br /&amp;gt; Vous pouvez gérer les paramètres de maintenance dans l&#039;onglet Maintenance de la page Paramètres de la boutique.</p>\" href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\"
          >
            <i class=\"material-icons\">build</i>
            <span>Mode maintenance</span>
          </a>
        </div>
      
      <div class=\"header-right\">
                  <div class=\"component\" id=\"header-shop-list-container\">
              <div class=\"shop-list\">
    <a class=\"link\"";
        // line 378
        echo " id=\"header_shopname\" href=\"https://dev.labonnegraine.com/\" target= \"_blank\">
      <i class=\"material-icons\">visibility</i>
      <span>Voir ma boutique</span>
    </a>
  </div>
          </div>
                
        <div class=\"component\" id=\"header-employee-container\">
          <div class=\"dropdown employee-dropdown\">
  <div class=\"rounded-circle person\" data-toggle=\"dropdown\">
    <i class=\"material-icons\">account_circle</i>
  </div>
  <div class=\"dropdown-menu dropdown-menu-right\">
    <div class=\"employee-wrapper-avatar\">
      <div class=\"employee-top\">
        <span class=\"employee-avatar\"><img class=\"avatar rounded-circle\" src=\"https://dev.labonnegraine.com/img/pr/default.jpg\" alt=\"Guillaume\" /></span>
        <span class=\"employee_profile\">Ravi de vous revoir Guillaume</span>
      </div>

      <a class=\"dropdown-item employee-link profile-link\" href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/17/edit\">
      <i class=\"material-icons\">edit</i>
      <span>Votre profil</span>
    </a>
    </div>

    <p class=\"divider\"></p>

    
    <a class=\"dropdown-item employee-link text-center\" id=\"header_logout\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminLogin&amp;logout=1\">
      <i class=\"material-icons d-lg-none\">power_settings_new</i>
      <span>Déconnexion</span>
    </a>
  </div>
</div>
        </div>
              </div>
    </nav>
  </header>

  <nav class=\"nav-bar d-none d-print-none d-md-block\">
  <span class=\"menu-collapse\" data-toggle-url=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/toggle-navigation\">
    <i class=\"material-icons rtl-flip\">chevron_left</i>
    <i class=\"material-icons rtl-flip\">chevron_left</i>
  </span>

  <div class=\"nav-bar-overflow\">
      <div class=\"logo-container\">
          <a id=\"header_logo\" class=\"logo float-left\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\"></a>
          <span id=\"shop_";
        // line 426
        echo "version\" class=\"header-version\">8.0.1</span>
      </div>

      <ul class=\"main-menu\">
              
                    
                    
          
            <li class=\"link-levelone\" data-submenu=\"1\" id=\"tab-AdminDashboard\">
              <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\" class=\"link\" >
                <i class=\"material-icons\">trending_up</i> <span>Tableau de bord</span>
              </a>
            </li>

          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"2\" id=\"tab-SELL\">
                <span class=\"title\">Vendre</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"3\" id=\"subtab-AdminParentOrders\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDmuListeCommandes\" class=\"link\">
                      <i class=\"material-icons mi-shopping_basket\">shopping_basket</i>
                      <span>
                      Commandes
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-3\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"207\" id=\"subtab-AdminDmuListeCommandes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?c";
        // line 468
        echo "ontroller=AdminDmuListeCommandes\" class=\"link\"> Commandes Améliorées
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"4\" id=\"subtab-AdminOrders\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"5\" id=\"subtab-AdminInvoices\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/invoices/\" class=\"link\"> Factures
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"6\" id=\"subtab-AdminSlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/credit-slips/\" class=\"link\"> Avoirs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"7\" id=\"subtab-AdminDeliverySlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/delivery-slips/\" class=\"link\"> ";
        // line 500
        echo "Bons de livraison
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"8\" id=\"subtab-AdminCarts\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarts\" class=\"link\"> Paniers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"132\" id=\"subtab-AdminControleCommande\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminControleCommande\" class=\"link\"> Contrôle des commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"135\" id=\"subtab-AdminBox\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminBox\" class=\"link\"> Box
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"204\" id=\"subtab-AdminDPDFrance\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDPDFrance\" class=\"link\"> DPD France livraison
 ";
        // line 533
        echo "                               </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"205\" id=\"subtab-AdminDPDFranceReturn\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDPDFranceReturn\" class=\"link\"> DPD France retour
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"9\" id=\"subtab-AdminCatalog\">
                    <a href=\"/admin123/index.php/sell/catalog/products\" class=\"link\">
                      <i class=\"material-icons mi-store\">store</i>
                      <span>
                      Catalogue
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-9\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"10\" id=\"subtab-AdminProducts\">
                                <a href=\"/admin123/index.php/sell/catalog/products\" class=\"link\"> Produits
                                </a>
               ";
        // line 567
        echo "               </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"11\" id=\"subtab-AdminCategories\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/categories\" class=\"link\"> Catégories
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"12\" id=\"subtab-AdminTracking\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/monitoring/\" class=\"link\"> Suivi
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"13\" id=\"subtab-AdminParentAttributesGroups\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminAttributesGroups\" class=\"link\"> Attributs et caractéristiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"16\" id=\"subtab-AdminParentManufacturers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/brands/\" class=\"link\"> Marques et fournisseurs
                             ";
        // line 598
        echo "   </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"19\" id=\"subtab-AdminAttachments\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/attachments/\" class=\"link\"> Fichiers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"20\" id=\"subtab-AdminParentCartRules\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules\" class=\"link\"> Réductions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"23\" id=\"subtab-AdminStockManagement\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/stocks/\" class=\"link\"> Stock
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"164\" id=\"subtab-AdminQuantityDiscountRules\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuantityDiscountRules\" class=\"link\"> Promotions and discounts
                               ";
        // line 630
        echo " </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"176\" id=\"subtab-MajQtyMasse\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=MajQtyMasse\" class=\"link\"> Mise a jour quantites de masse
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"177\" id=\"subtab-MajmsgMasse\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=MajmsgMasse\" class=\"link\"> Mise a jour messages de masse
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"203\" id=\"subtab-FichesEnvoiClientTab\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=FichesEnvoiClientTab\" class=\"link\"> Envoi des fiches aux clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"247\" id=\"subtab-AdminCategoryHeaderMessages\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCategoryHeaderMessage";
        // line 661
        echo "s\" class=\"link\"> Messages commerciaux
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"24\" id=\"subtab-AdminParentCustomer\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customers/\" class=\"link\">
                      <i class=\"material-icons mi-account_circle\">account_circle</i>
                      <span>
                      Clients
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-24\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"25\" id=\"subtab-AdminCustomers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customers/\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"26\" id=\"subtab-AdminAddresses\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/addresses";
        // line 694
        echo "/\" class=\"link\"> Adresses
                                </a>
                              </li>

                                                                                                                                        
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"242\" id=\"subtab-AdminLiveCartReminder\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminLiveCartReminder\" class=\"link\"> Relance panier
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"28\" id=\"subtab-AdminParentCustomerThreads\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCustomerThreads\" class=\"link\">
                      <i class=\"material-icons mi-chat\">chat</i>
                      <span>
                      SAV
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-28\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"29\" id=\"subtab-AdminCustomerThreads\">
                         ";
        // line 727
        echo "       <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCustomerThreads\" class=\"link\"> SAV
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"30\" id=\"subtab-AdminOrderMessage\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customer-service/order-messages/\" class=\"link\"> Messages prédéfinis
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"31\" id=\"subtab-AdminReturn\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminReturn\" class=\"link\"> Retours produits
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"32\" id=\"subtab-AdminStats\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats\" class=\"link\">
                      <i class=\"material-icons mi-assessment\">assessment</i>
                      <span>
                      Statistiques
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                              ";
        // line 760
        echo "      keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"37\" id=\"tab-IMPROVE\">
                <span class=\"title\">Personnaliser</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"38\" id=\"subtab-AdminParentModulesSf\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Modules
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-38\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"39\" id=\"subtab-AdminModulesSf\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage\" class=\"link\"> Gestionnaire de modules 
                                </a>
                              </li>

                                                                                                                                        
   ";
        // line 798
        echo "                           
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"208\" id=\"subtab-AdminStoreCommander\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStoreCommander\" class=\"link\"> Store Commander
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"43\" id=\"subtab-AdminParentThemes\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/themes/\" class=\"link\">
                      <i class=\"material-icons mi-desktop_mac\">desktop_mac</i>
                      <span>
                      Apparence
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-43\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"124\" id=\"subtab-AdminThemesParent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/themes/\" class=\"link\"> Thème et logo
                                </a>
                              </li>

                                                   ";
        // line 830
        echo "                               
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"45\" id=\"subtab-AdminParentMailTheme\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/mail_theme/\" class=\"link\"> Thème d&#039;e-mail
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"47\" id=\"subtab-AdminCmsContent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/cms-pages/\" class=\"link\"> Pages
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"48\" id=\"subtab-AdminModulesPositions\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/modules/positions/\" class=\"link\"> Positions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"49\" id=\"subtab-AdminImages\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminImages\" class=\"link\"> Images
                                </a>
                              </li>

                                                              ";
        // line 862
        echo "                    
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"117\" id=\"subtab-AdminLinkWidget\">
                                <a href=\"/admin123/index.php/modules/link-widget/list\" class=\"link\"> Liste de liens
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"233\" id=\"subtab-AdminParentCEContent\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEThemes\" class=\"link\">
                      <i class=\"material-icons mi-ce\">ce</i>
                      <span>
                      Creative Elements
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-233\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"234\" id=\"subtab-AdminCEThemes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEThemes\" class=\"link\"> Constructeur de thème
                                </a>
                              </li>

                                                                ";
        // line 895
        echo "                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"235\" id=\"subtab-AdminCEContent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEContent\" class=\"link\"> Contenu n’importe où
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"236\" id=\"subtab-AdminCETemplates\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCETemplates\" class=\"link\"> Modèles enregistrés
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"237\" id=\"subtab-AdminParentCEFonts\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEFonts\" class=\"link\"> Polices &amp; Icônes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"240\" id=\"subtab-AdminCESettings\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCESettings\" class=\"link\"> Réglages
                                </a>
                              </li>

                                              ";
        // line 927
        echo "                                                                                      </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"50\" id=\"subtab-AdminParentShipping\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarriers\" class=\"link\">
                      <i class=\"material-icons mi-local_shipping\">local_shipping</i>
                      <span>
                      Livraison
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-50\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"51\" id=\"subtab-AdminCarriers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarriers\" class=\"link\"> Transporteurs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"52\" id=\"subtab-AdminShipping\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/shipping/preferences/\" class=\"link\"> Préférences
                                </a";
        // line 957
        echo ">
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"170\" id=\"subtab-AdminColissimoDashboard\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoDashboard\" class=\"link\"> Colissimo - Tableau de bord
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"171\" id=\"subtab-AdminColissimoAffranchissement\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoAffranchissement\" class=\"link\"> Colissimo - Affranchissement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"172\" id=\"subtab-AdminColissimoDepositSlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoDepositSlip\" class=\"link\"> Colissimo - Bordereaux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"173\" id=\"subtab-AdminColissimoColiship\">
                                <a href=\"https://dev.labonnegraine.com/adm";
        // line 988
        echo "in123/index.php?controller=AdminColissimoColiship\" class=\"link\"> Colissimo - Coliship
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"174\" id=\"subtab-AdminColissimoCustomsDocuments\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoCustomsDocuments\" class=\"link\"> Colissimo - Documents
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"53\" id=\"subtab-AdminParentPayment\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/payment_methods\" class=\"link\">
                      <i class=\"material-icons mi-payment\">payment</i>
                      <span>
                      Paiement
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-53\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"54\" id=\"subtab-AdminPayment\">";
        // line 1020
        echo "
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/payment_methods\" class=\"link\"> Moyens de paiement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"55\" id=\"subtab-AdminPaymentPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/preferences\" class=\"link\"> Préférences
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"210\" id=\"subtab-AdminPaymentFee\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPaymentFee\" class=\"link\"> Les frais de paiement
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"56\" id=\"subtab-AdminInternational\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/localization/\" class=\"link\">
                      <i class=\"material-icons mi-language\">language</i>
                      <span>
                      International
                      </span>
                                                    <i class=\"material-icons sub-t";
        // line 1053
        echo "abs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-56\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"57\" id=\"subtab-AdminParentLocalization\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/localization/\" class=\"link\"> Localisation
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"62\" id=\"subtab-AdminParentCountries\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/zones/\" class=\"link\"> Zones géographiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"66\" id=\"subtab-AdminParentTaxes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/taxes/\" class=\"link\"> Taxes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                     ";
        // line 1085
        echo "         <li class=\"link-leveltwo\" data-submenu=\"69\" id=\"subtab-AdminTranslations\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/translations/settings\" class=\"link\"> Traductions
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                                            
          
                      
                                          
                    
          
            <li class=\"category-title link-active\" data-submenu=\"70\" id=\"tab-CONFIGURE\">
                <span class=\"title\">Configurer</span>
            </li>

                              
                  
                                                      
                                                          
                  <li class=\"link-levelone has_submenu link-active open ul-open\" data-submenu=\"71\" id=\"subtab-ShopParameters\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" class=\"link\">
                      <i class=\"material-icons mi-settings\">settings</i>
                      <span>
                      Paramètres de la boutique
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_up
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-71\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo link-active\" data-su";
        // line 1120
        echo "bmenu=\"72\" id=\"subtab-AdminParentPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" class=\"link\"> Paramètres généraux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"75\" id=\"subtab-AdminParentOrderPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/order-preferences/\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"78\" id=\"subtab-AdminPPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/product-preferences/\" class=\"link\"> Produits
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"79\" id=\"subtab-AdminParentCustomerPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/customer-preferences/\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
   ";
        // line 1152
        echo "                           <li class=\"link-leveltwo\" data-submenu=\"83\" id=\"subtab-AdminParentStores\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/contacts/\" class=\"link\"> Contact
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"86\" id=\"subtab-AdminParentMeta\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/seo-urls/\" class=\"link\"> Trafic et SEO
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"89\" id=\"subtab-AdminParentSearchConf\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminSearchConf\" class=\"link\"> Rechercher
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"92\" id=\"subtab-AdminAdvancedParameters\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/system-information/\" class=\"link\">
                      <i class=\"material-icons mi-settings_applications\">settings_applications</i>
                      <span>
                      Paramètres avancés
     ";
        // line 1184
        echo "                 </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-92\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"93\" id=\"subtab-AdminInformation\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/system-information/\" class=\"link\"> Informations
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"94\" id=\"subtab-AdminPerformance\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/performance/\" class=\"link\"> Performances
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"95\" id=\"subtab-AdminAdminPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/administration/\" class=\"link\"> Administration
                                </a>
                              </li>

                                                                                  ";
        // line 1214
        echo "
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"96\" id=\"subtab-AdminEmails\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/emails/\" class=\"link\"> E-mail
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"97\" id=\"subtab-AdminImport\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/import/\" class=\"link\"> Importer
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"98\" id=\"subtab-AdminParentEmployees\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/\" class=\"link\"> Équipe
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"102\" id=\"subtab-AdminParentRequestSql\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/sql-requests/\" class=\"link\"> Base de données
                                </a>
                              </li>

                                                                                  
         ";
        // line 1247
        echo "                     
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"105\" id=\"subtab-AdminLogs\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/logs/\" class=\"link\"> Logs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"106\" id=\"subtab-AdminWebservice\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/webservice-keys/\" class=\"link\"> Webservice
                                </a>
                              </li>

                                                                                                                                                                                              
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"109\" id=\"subtab-AdminFeatureFlag\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/feature-flags/\" class=\"link\"> Fonctionnalités nouvelles et expérimentales
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"110\" id=\"subtab-AdminParentSecurity\">
                                <a href=\"/admin123/index.php/configure/advanced/security/\" class=\"link\"> Sécurité
                                </a>
                      ";
        // line 1276
        echo "        </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"206\" id=\"subtab-AdminCdcGoogletagmanagerOrders\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCdcGoogletagmanagerOrders\" class=\"link\"> GTM Orders
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"142\" id=\"tab-AdminUTMTab\">
                <span class=\"title\">UTM</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"143\" id=\"subtab-AdminUtmStats\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminUtmStats\" class=\"link\">
                      <i class=\"material-icons mi-settings\">settings</i>
                      <span>
                      StatsUTM
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"144\" id=\"tab-";
        // line 1319
        echo "PsaffiliateAdmin\">
                <span class=\"title\">PS Affiliate</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"145\" id=\"subtab-AdminPsaffiliateAdmin\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateAdmin\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Tableau de bord
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"146\" id=\"subtab-AdminPsaffiliateConfiguration\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateConfiguration\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Paramètres
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
              ";
        // line 1357
        echo "    <li class=\"link-levelone\" data-submenu=\"147\" id=\"subtab-AdminPsaffiliateAffiliates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateAffiliates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Affiliates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"148\" id=\"subtab-AdminPsaffiliateCustomFields\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCustomFields\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Affiliates Custom Fields
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"149\" id=\"subtab-AdminPsaffiliatePayments\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliatePayments\" class=\"link\">
";
        // line 1389
        echo "                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Payments
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"150\" id=\"subtab-AdminPsaffiliatePaymentMethods\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliatePaymentMethods\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Moyens de paiement
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"151\" id=\"subtab-AdminPsaffiliateBanners\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateBanners\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Banners
                      </span>
                                                    <i class=\"";
        // line 1423
        echo "material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"152\" id=\"subtab-AdminPsaffiliateTexts\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateTexts\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Text Ads
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"153\" id=\"subtab-AdminPsaffiliateRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      General Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
        ";
        // line 1457
        echo "                                </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"154\" id=\"subtab-AdminPsaffiliateCategoryRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCategoryRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Category Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"155\" id=\"subtab-AdminPsaffiliateProductRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateProductRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Product Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                 ";
        // line 1492
        echo " <li class=\"link-levelone\" data-submenu=\"156\" id=\"subtab-AdminPsaffiliateTraffic\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateTraffic\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Trafic
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"157\" id=\"subtab-AdminPsaffiliateSales\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateSales\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Sales
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"158\" id=\"subtab-AdminPsaffiliateCampaigns\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCampaigns\" class=\"link\">
                      <i class=\"material-ico";
        // line 1524
        echo "ns mi-extension\">extension</i>
                      <span>
                      Campaigns
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"159\" id=\"subtab-AdminPsaffiliateStatistics\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateStatistics\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Statistics
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"182\" id=\"tab-AdminEtsAm\">
                <span class=\"title\">Programmes de marketing</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"183\" id=\"subtab-AdminEtsAmDashboard\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmDashboard\" class=\"link\">
    ";
        // line 1564
        echo "                  <i class=\"material-icons mi-eam_show_chart fa fa-line-chart \">eam_show_chart fa fa-line-chart </i>
                      <span>
                      Tableau de bord
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"184\" id=\"subtab-AdminEtsAmMarketing\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmLoyalty\" class=\"link\">
                      <i class=\"material-icons mi-marketing\">marketing</i>
                      <span>
                      Marketing program
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-184\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"185\" id=\"subtab-AdminEtsAmLoyalty\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmLoyalty\" class=\"link\"> Programme de fidélité 
                                </a>
                              </li>

                  ";
        // line 1596
        echo "                                                                
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"186\" id=\"subtab-AdminEtsAmRS\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRS\" class=\"link\"> Programme de parrainage 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"187\" id=\"subtab-AdminEtsAmAffiliate\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmAffiliate\" class=\"link\"> Programme d&#039;affiliation 
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"188\" id=\"subtab-AdminEtsAmRewards\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRU\" class=\"link\">
                      <i class=\"material-icons mi-rewards\">rewards</i>
                      <span>
                      Rewards
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                          ";
        // line 1628
        echo "    <ul id=\"collapse-188\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"189\" id=\"subtab-AdminEtsAmRU\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRU\" class=\"link\"> Utilisation de la récompense
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"190\" id=\"subtab-AdminEtsAmRewardHistory\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRewardHistory\" class=\"link\"> Récompense l&#039;histoire
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"191\" id=\"subtab-AdminEtsAmWithdrawals\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmWithdrawals\" class=\"link\"> Retrait
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"192\" id=\"subtab-AdminEtsAmCustomers\">
                    <a href=\"https://dev.labonneg";
        // line 1660
        echo "raine.com/admin123/index.php?controller=AdminEtsAmApp\" class=\"link\">
                      <i class=\"material-icons mi-customer\">customer</i>
                      <span>
                      Clients
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-192\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"193\" id=\"subtab-AdminEtsAmApp\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmApp\" class=\"link\"> Candidatures
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"194\" id=\"subtab-AdminEtsAmUsers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmUsers\" class=\"link\"> Utilisateurs
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"195\" id=\"subtab-AdminEtsAmBackup\">
                    <a href=\"https://dev.labon";
        // line 1693
        echo "negraine.com/admin123/index.php?controller=AdminEtsAmBackup\" class=\"link\">
                      <i class=\"material-icons mi-swap_horiz\">swap_horiz</i>
                      <span>
                      Restauration / Sauvegarde
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"196\" id=\"subtab-AdminEtsAmCronjob\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmCronjob\" class=\"link\">
                      <i class=\"material-icons mi-tasks\">tasks</i>
                      <span>
                      Cronjob
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"197\" id=\"subtab-AdminEtsAmGeneral\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmGeneral\" class=\"link\">
                      <i class=\"material-icons mi-cogs\">cogs</i>
                      <span>
                      Réglages généraux
                      </span>
                      ";
        // line 1728
        echo "                              <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"222\" id=\"tab-AdminYbcBlog\">
                <span class=\"title\">Blog</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"223\" id=\"subtab-AdminYbcBlogPost\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminPriceRule\">icon icon-AdminPriceRule</i>
                      <span>
                      Articles
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"224\" id=\"subtab-AdminYbcBlogCategory\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogCategory\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminCatalog\">icon icon-AdminCatalog</i>
                      <span>
                      Cat";
        // line 1766
        echo "égories
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"225\" id=\"subtab-AdminYbcBlogComment\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogComment\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-comments\">icon icon-comments</i>
                      <span>
                      Commentaires
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"226\" id=\"subtab-AdminYbcBlogPolls\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPolls\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-polls\">icon icon-polls</i>
                      <span>
                      Sondages
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
               ";
        // line 1800
        echo "                                             </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"227\" id=\"subtab-AdminYbcBlogSlider\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogSlider\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminParentModules\">icon icon-AdminParentModules</i>
                      <span>
                      Glissière
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"228\" id=\"subtab-AdminYbcBlogGallery\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogGallery\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminDashboard\">icon icon-AdminDashboard</i>
                      <span>
                      Galerie photo
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
 ";
        // line 1834
        echo "                 
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"229\" id=\"subtab-AdminYbcBlogAuthor\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogAuthor\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-user\">icon icon-user</i>
                      <span>
                      Auteurs
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"230\" id=\"subtab-AdminYbcBlogStatistics\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogStatistics\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-chart\">icon icon-chart</i>
                      <span>
                      Statistiques
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"231\" id=\"subtab-AdminYbcBlogBackUp\">
                    <a href=\"https://dev.labonnegr";
        // line 1868
        echo "aine.com/admin123/index.php?controller=AdminYbcBlogBackUp\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-exchange\">icon icon-exchange</i>
                      <span>
                      Importer / Exporter
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"232\" id=\"subtab-AdminYbcBlogSetting\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogSetting\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminAdmin\">icon icon-AdminAdmin</i>
                      <span>
                      Paramètres globaux
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                  </ul>
  </div>
  
</nav>


<div class=\"header-toolbar d-print-none\">
    
  <div class=\"container-fluid\">

    
      <nav aria-label=\"Breadcrumb\">
        <ol class=\"breadcrumb\">
                      <li class=\"breadcrumb-item\">Paramètres généraux</li>
          
                      <li class=\"breadcrumb-item active\">
              <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\" ari";
        // line 1911
        echo "a-current=\"page\">Maintenance</a>
            </li>
                  </ol>
      </nav>
    

    <div class=\"title-row\">
      
          <h1 class=\"title\">
            Maintenance          </h1>
      

      
        <div class=\"toolbar-icons\">
          <div class=\"wrapper\">
            
                        
            
                              <a class=\"btn btn-outline-secondary btn-help btn-sidebar\" href=\"#\"
                   title=\"Aide\"
                   data-toggle=\"sidebar\"
                   data-target=\"#right-sidebar\"
                   data-url=\"/admin123/index.php/common/sidebar/https%253A%252F%252Fhelp.prestashop-project.org%252Ffr%252Fdoc%252FAdminMaintenance%253Fversion%253D8.0.1%2526country%253Dfr/Aide\"
                   id=\"product_form_open_help\"
                >
                  Aide
                </a>
                                    </div>
        </div>

      
    </div>
  </div>

  
      <div class=\"page-head-tabs\" id=\"head_tabs\">
      <ul class=\"nav nav-pills\">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ";
        // line 1948
        echo "                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <li class=\"nav-item\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" id=\"subtab-AdminPreferences\" class=\"nav-link tab \" data-submenu=\"73\">
                      Paramètres généraux
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                <li class=\"nav-item\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\" id=\"subtab-AdminMaintenance\" class=\"nav-link tab active current\" data-submenu=\"74\">
                      Maintenance
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                                                                                                                                                                                                                                                                                              ";
        // line 1964
        echo "                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </ul>
    </div>
  
  <div class=\"btn-floating\">
    <button class=\"btn btn-primary collapsed\" data-toggle=\"collapse\" data-target=\".btn-floating-container\" aria-expanded=\"false\">
      <i class=\"material-icons\">add</i>
    </button>
    <div class=\"btn-floating-container collapse\">
      <div class=\"btn-floating-menu\">
        
        
                              <a class=\"btn btn-floating-item btn-help btn-sidebar\" href=\"#\"
               title=\"Aide\"
               data-toggle=\"sidebar\"
               data-target=\"#right-sidebar\"
               data-url=\"/admin123/index.php/common/sidebar/https%253A%252F%252Fhelp.prestashop-project.org%252Ffr%252Fdoc%252FAdminMaintenance%253Fversion%253D8.0.1%2526country%253Dfr/Aide\"
            >
              Aide
            </a>
                        </div>
    </div>
  </div>
  
</div>

<div id=\"main-div\">
          
      <div class=\"content-div  with-tabs\">

        

                                                 ";
        // line 1995
        echo "       
        <div id=\"ajax_confirmation\" class=\"alert alert-success\" style=\"display: none;\"></div>
<div id=\"content-message-box\"></div>


  ";
        // line 2000
        $this->displayBlock('content_header', $context, $blocks);
        $this->displayBlock('content', $context, $blocks);
        $this->displayBlock('content_footer', $context, $blocks);
        $this->displayBlock('sidebar_right', $context, $blocks);
        echo "

        

      </div>
    </div>

  <div id=\"non-responsive\" class=\"js-non-responsive\">
  <h1>Oh non !</h1>
  <p class=\"mt-3\">
    La version mobile de cette page n'est pas encore disponible.
  </p>
  <p class=\"mt-2\">
    Cette page n'est pas encore disponible sur mobile, merci de la consulter sur ordinateur.
  </p>
  <p class=\"mt-2\">
    Merci.
  </p>
  <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\" class=\"btn btn-primary py-1 mt-3\">
    <i class=\"material-icons rtl-flip\">arrow_back</i>
    Précédent
  </a>
</div>
  <div class=\"mobile-layer\"></div>

      <div id=\"footer\" class=\"bootstrap\">
    <script type=\"text/javascript\">
var link_ajax='https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&configure=ybc_blog&tab_module=front_office_features&module_name=ybc_blog';
\$(document).ready(function(){
    \$.ajax({
        url: link_ajax,
        data: 'action=getCountMessageYbcBlog',
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            if(parseInt(json.count) >0)
            {
                if(\$('#subtab-AdminYbcBlogComment span').length)
                    \$('#subtab-AdminYbcBlogComment span').append('<span class=\"count_messages \">'+json.count+'</span>'); 
                else
                    \$('#subtab-AdminYbcBlogComment a').append('<span class=\"count_messages \">'+json.count+'</span>');
            }
            else
            {
                if(\$('#subtab-AdminYbcBlogComment span').length)
                    \$('#subtab-AdminYbcBlogComment span').append('<span class=\"count_messages hide\">'+json.count+'</span>'); 
                else
                    \$('#subtab-AdminYbcBlogComment a').append('<span class=\"count_messages hide\">'+json.count+'</span>');
            }
                                                              
        },
    });
});
</script>
</div>
  

      <div class=\"bootstrap\">
      
    </div>
  
";
        // line 2061
        $this->displayBlock('javascripts', $context, $blocks);
        $this->displayBlock('extra_javascripts', $context, $blocks);
        $this->displayBlock('translate_javascripts', $context, $blocks);
        echo "</body>";
        echo "
</html>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 141
    public function block_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_extra_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "extra_stylesheets"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "extra_stylesheets"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 2000
    public function block_content_header($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content_header"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content_header"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_content_footer($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content_footer"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content_footer"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_sidebar_right($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar_right"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar_right"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 2061
    public function block_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_extra_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "extra_javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "extra_javascripts"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function block_translate_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "translate_javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "translate_javascripts"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "__string_template__73ecd62107498bcd0e80e8a2f8a1b7d6b4551b5e7cc6059d7dd53493f3b00269";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  2362 => 2061,  2293 => 2000,  2258 => 141,  2243 => 2061,  2176 => 2000,  2169 => 1995,  2136 => 1964,  2118 => 1948,  2079 => 1911,  2034 => 1868,  1998 => 1834,  1962 => 1800,  1926 => 1766,  1886 => 1728,  1849 => 1693,  1814 => 1660,  1780 => 1628,  1746 => 1596,  1712 => 1564,  1670 => 1524,  1636 => 1492,  1599 => 1457,  1563 => 1423,  1527 => 1389,  1493 => 1357,  1453 => 1319,  1408 => 1276,  1377 => 1247,  1342 => 1214,  1310 => 1184,  1276 => 1152,  1242 => 1120,  1205 => 1085,  1171 => 1053,  1136 => 1020,  1102 => 988,  1069 => 957,  1037 => 927,  1003 => 895,  968 => 862,  934 => 830,  900 => 798,  860 => 760,  825 => 727,  790 => 694,  755 => 661,  722 => 630,  688 => 598,  655 => 567,  619 => 533,  584 => 500,  550 => 468,  506 => 426,  456 => 378,  414 => 338,  375 => 301,  339 => 267,  317 => 247,  275 => 207,  245 => 179,  202 => 141,  198 => 139,  146 => 89,  125 => 70,  96 => 43,  52 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{{ '<!DOCTYPE html>
<html lang=\"fr\">
<head>
  <meta charset=\"utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
<meta name=\"robots\" content=\"NOFOLLOW, NOINDEX\">

<link rel=\"icon\" type=\"image/x-icon\" href=\"/img/favicon.ico\" />
<link rel=\"apple-touch-icon\" href=\"/img/app_icon.png\" />

<title>Maintenance • La Bonne Graine</title>

  <script type=\"text/javascript\">
    var help_class_name = \\'AdminMaintenance\\';
    var iso_user = \\'fr\\';
    var lang_is_rtl = \\'0\\';
    var full_language_code = \\'fr\\';
    var full_cldr_language_code = \\'fr-FR\\';
    var country_iso_code = \\'FR\\';
    var _PS_VERSION_ = \\'8.0.1\\';
    var roundMode = 2;
    var youEditFieldFor = \\'\\';
        var new_order_msg = \\'Une nouvelle commande a été passée sur votre boutique.\\';
    var order_number_msg = \\'Numéro de commande : \\';
    var total_msg = \\'Total : \\';
    var from_msg = \\'Du \\';
    var see_order_msg = \\'Afficher cette commande\\';
    var new_customer_msg = \\'Un nouveau client s\\\\\\'est inscrit sur votre boutique\\';
    var customer_name_msg = \\'Nom du client : \\';
    var new_msg = \\'Un nouveau message a été posté sur votre boutique.\\';
    var see_msg = \\'Lire le message\\';
    var token = \\'c0c92e1f879658cfb998234f752e7a25\\';
    var token_admin_orders = tokenAdminOrders = \\'2dae91902180567ff334d3da06e9e6c3\\';
    var token_admin_customers = \\'60299276d5706a56cfcb320d277af8de\\';
    var token_admin_customer_threads = tokenAdminCustomerThreads = \\'d4480d95d5e21e52437a51288ee1b582\\';
    var currentIndex = \\'index.php?controller=AdminMaintenance\\';
    var employee_token = \\'121823b38070a62abc4d0c663acc4a02\\';
    var choose_language_translate = \\'Choisissez la langue :\\';
    var default_language = \\'1\\';
    var admin_modules_link = \\'https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage\\';
    var admin_notification_get_link = \\'https://dev.labonnegraine.com/admin123/index.php/common/notifications\\';
    var admin_notification_' | raw }}{{ 'push_link = adminNotificationPushLink = \\'https://dev.labonnegraine.com/admin123/index.php/common/notifications/ack\\';
    var tab_modules_list = \\'\\';
    var update_success_msg = \\'Mise à jour réussie\\';
    var search_product_msg = \\'Rechercher un produit\\';
  </script>



<link
      rel=\"preload\"
      href=\"/admin123/themes/new-theme/public/auto703cf8f274fbb265d49c6262825780e1.preload.woff2\"
      as=\"font\"
      crossorigin
    >
      <link href=\"/admin123/themes/new-theme/public/theme.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/chosen/jquery.chosen.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/fancybox/jquery.fancybox.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/blockwishlist/public/backoffice.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/admin123/themes/default/css/vendor/nv.d3.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/creativeelements/views/css/admin-ce.css?v=2.11.0\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/psaffiliate/views/css/back.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ets_affiliatemarketing/views/css/admin_all.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ybc_blog/views/css/admin_all.css\" rel=\"stylesheet\" type=\"text/css\"/>
  
  <script type=\"text/javascript\">
var baseAdminDir = \"\\\\/admin123\\\\/\";
var baseDir = \"\\\\/\";
var ceAdmin = {\"uid\":\"0020001\",\"hideEditor\":[],\"footerProduct\":\"\",\"i18n\":{\"edit\":\"Edit with Creative Elements\",\"save\":\"Veuillez enregistrer le formulaire avant de le modifier avec Creative Elements.\"},\"editorUrl\":\"https:\\\\/\\\\/dev.labonnegraine.com\\\\/admin123\\\\/index.php?controller=AdminCEEditor&amp;uid=\",\"languages\":[{\"id_lang\":\"1\",\"name\":\"Fran\\\\u00e7ais (French)\",\"active\":\"1\",\"iso_code\":\"fr\",\"language_code\":\"fr\",\"locale\":\"fr-FR\",\"date_format_lite\":\"d\\\\/m\\\\/Y\",\"date_format_full\":\"d\\\\/m\\\\/Y H:i:s\",\"is_rtl\":\"0\",\"id_shop\":\"1\",\"shops\":{\"1\":true}}],\"editSuppliers\":0,\"edit' | raw }}{{ 'Manufacturers\":0};
var changeFormLanguageUrl = \"https:\\\\/\\\\/dev.labonnegraine.com\\\\/admin123\\\\/index.php\\\\/configure\\\\/advanced\\\\/employees\\\\/change-form-language\";
var currency = {\"iso_code\":\"EUR\",\"sign\":\"\\\\u20ac\",\"name\":\"Euro\",\"format\":null};
var currency_specifications = {\"symbol\":[\",\",\"\\\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\\\u00d7\",\"\\\\u2030\",\"\\\\u221e\",\"NaN\"],\"currencyCode\":\"EUR\",\"currencySymbol\":\"\\\\u20ac\",\"numberSymbols\":[\",\",\"\\\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\\\u00d7\",\"\\\\u2030\",\"\\\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.00\\\\u00a0\\\\u00a4\",\"negativePattern\":\"-#,##0.00\\\\u00a0\\\\u00a4\",\"maxFractionDigits\":2,\"minFractionDigits\":2,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var number_specifications = {\"symbol\":[\",\",\"\\\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\\\u00d7\",\"\\\\u2030\",\"\\\\u221e\",\"NaN\"],\"numberSymbols\":[\",\",\"\\\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\\\u00d7\",\"\\\\u2030\",\"\\\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.###\",\"negativePattern\":\"-#,##0.###\",\"maxFractionDigits\":3,\"minFractionDigits\":0,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var prestashop = {\"debug\":true};
var show_new_customers = \"\";
var show_new_messages = \"\";
var show_new_orders = \"\";
</script>
<script type=\"text/javascript\" src=\"/admin123/themes/new-theme/public/main.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/jquery.chosen.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/fancybox/jquery.fancybox.js\"></script>
<script type=\"text/javascript\" src=\"/js/admin.js?v=8.0.1\"></script>
<script type=\"text/javascript\" src=\"/admin123/themes/new-theme/public/cldr.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/tools.js?v=8.0.1\"></script>
<script type=\"text/javascript\" src=\"/modules/blockwishlist/public/vendors.js\"></script>
<script type=\"text/javascript\" src=\"/js/vendor/d3.v3.min.js\"></script>
<script type=\"text/javascript\" src=\"/admin123/themes/default/js/vendor/nv.d3.min.js\"></script>
<script type=\"text/javascript\" src=\"/modules/creativeelements/views/js/admin-ce.js?v=2' | raw }}{{ '.11.0\"></script>
<script type=\"text/javascript\" src=\"/modules/ets_affiliatemarketing/views/js/admin_all.js\"></script>
<script type=\"text/javascript\" src=\"/modules/aw_mailsmailjet/views/js/aw_mailsmailjet_back_office.js\"></script>
<script type=\"text/javascript\" src=\"/modules/aw_impressionadresse/aw_impressionadresse.js?v=6\"></script>
<script type=\"text/javascript\" src=\"/modules/awproduct/views/js/custom.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ybc_blog/views/js/admin_all.js\"></script>

  <style>
i.mi-ce {
\tfont-size: 14px !important;
}
i.icon-AdminParentCEContent, i.mi-ce {
\tposition: relative;
\theight: 1em;
\twidth: 1.2857em;
}
i.icon-AdminParentCEContent:before, i.mi-ce:before,
i.icon-AdminParentCEContent:after, i.mi-ce:after {
\tcontent: \\'\\';
\tposition: absolute;
\tmargin: 0;
\tleft: .2143em;
\ttop: 0;
\twidth: .9286em;
\theight: .6428em;
\tborder-width: .2143em 0;
\tborder-style: solid;
\tborder-color: currentColor;
\tbox-sizing: content-box;
}
i.icon-AdminParentCEContent:after, i.mi-ce:after {
\ttop: .4286em;
\twidth: .6428em;
\theight: 0;
\tborder-width: .2143em 0 0;
}
#maintab-AdminParentCreativeElements, #subtab-AdminParentCreativeElements {
\tdisplay: none;
}
</style>
<script type=\"text/html\" id=\"tmpl-btn-back-to-ps\">
    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEEditor&amp;action=backToPsEditor\" class=\"btn btn-default btn-back-to-ps\"><i class=\"material-icons\">navigate_before</i> Revenir à l’éditeur PrestaShop</a>
</script>
<script type=\"text/html\" id=\"tmpl-btn-edit-with-ce\">
    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEEditor\" class=\"btn pointer btn-edit-with-ce\"><i class=\"material-icons mi-ce\"></i> Modifier avec Creative Elements</a>
</script>
<script type=\"application/javascript\">
                        \$(document).ready(function() {
                            \$.get(\"/modules/dpdfrance/cron.php?token=df0f15ff0991cb6ed8a899c3f9207206&employee=17\");
                        });
     ' | raw }}{{ '               </script>

' | raw }}{% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}</head>{{ '

<body
  class=\"lang-fr adminmaintenance\"
  data-base-url=\"/admin123/index.php\"  data-token=\"h5q5Vo6aGP6PuYPemGXjALLHLGbXHsJk5GilpTp8VAQ\">

  <header id=\"header\" class=\"d-print-none\">

    <nav id=\"header_infos\" class=\"main-header\">
      <button class=\"btn btn-primary-reverse onclick btn-lg unbind ajax-spinner\"></button>

            <i class=\"material-icons js-mobile-menu\">menu</i>
      <a id=\"header_logo\" class=\"logo float-left\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\"></a>
      <span id=\"shop_version\">8.0.1</span>

      <div class=\"component\" id=\"quick-access-container\">
        <div class=\"dropdown quick-accesses\">
  <button class=\"btn btn-link btn-sm dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" id=\"quick_select\">
    Accès rapide
  </button>
  <div class=\"dropdown-menu\">
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost&amp;token=fd0d13e8606c3aa0c326c11992702f8c\"
         target=\"_blank\"         data-item=\"Blog\"
      >Blog</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders?token=4abc6762ed44956188f556510f9a700f\"
                 data-item=\"Commandes\"
      >Commandes</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/../../controle.php?token=4abc6762ed44956188f556510f9a700f\"
         target=\"_blank\"         data-item=\"Contrôle Code Barre\"
      >Contrôle Code Barre</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statsstocksinventaire&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
                 data-item=\"Etat des stocks / Inventaire\"
      >Etat des stocks / Inventaire</a>
          <a class=\"dropdown-item quick-row-link\"
    ' | raw }}{{ '     href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statscheckup&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
                 data-item=\"Évaluation du catalogue\"
      >Évaluation du catalogue</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ets_megamenu&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Mega Menu\"
      >Mega Menu</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=moduleperso&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Module Perso\"
      >Module Perso</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage?token=4abc6762ed44956188f556510f9a700f\"
                 data-item=\"Modules installés\"
      >Modules installés</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules&amp;addcart_rule&amp;token=f8f9e6419a666825ad443054d4da45ab\"
                 data-item=\"Nouveau bon de réduction\"
      >Nouveau bon de réduction</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=homecategories&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"Produits phares\"
      >Produits phares</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ps_imageslider&amp;token=1a74a9290309a098688add3a3b4458a7\"
                 data-item=\"SLIDER\"
      >SLIDER</a>
          <a class=\"dropdown-item quick-row-link\"
         href=\"https://dev.labonnegraine.com/admin123/index.php?control' | raw }}{{ 'ler=AdminFlashSales&amp;token=28209f1f2d55b6c1fc5f31f26c7e9f95\"
                 data-item=\"Ventes Flash\"
      >Ventes Flash</a>
        <div class=\"dropdown-divider\"></div>
          <a id=\"quick-add-link\"
        class=\"dropdown-item js-quick-link\"
        href=\"#\"
        data-rand=\"8\"
        data-icon=\"icon-AdminParentPreferences\"
        data-method=\"add\"
        data-url=\"index.php/configure/shop/maintenance\"
        data-post-link=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\"
        data-prompt-text=\"Veuillez nommer ce raccourci :\"
        data-link=\"Maintenance - Liste\"
      >
        <i class=\"material-icons\">add_circle</i>
        Ajouter la page actuelle à l\\'accès rapide
      </a>
        <a id=\"quick-manage-link\" class=\"dropdown-item\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\">
      <i class=\"material-icons\">settings</i>
      Gérez vos accès rapides
    </a>
  </div>
</div>
      </div>
      <div class=\"component component-search\" id=\"header-search-container\">
        <div class=\"component-search-body\">
          <div class=\"component-search-top\">
            <form id=\"header_search\"
      class=\"bo_search_form dropdown-form js-dropdown-form collapsed\"
      method=\"post\"
      action=\"/admin123/index.php?controller=AdminSearch&amp;token=118f2de0383c2d76d0c8bc6aff8d6cb3\"
      role=\"search\">
  <input type=\"hidden\" name=\"bo_search_type\" id=\"bo_search_type\" class=\"js-search-type\" />
    <div class=\"input-group\">
    <input type=\"text\" class=\"form-control js-form-search\" id=\"bo_query\" name=\"bo_query\" value=\"\" placeholder=\"Rechercher (ex. : référence produit, nom du client, etc.)\" aria-label=\"Barre de recherche\">
    <div class=\"input-group-append\">
      <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle js-dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
        Partout
      </button>
      <div class=\"dropdown' | raw }}{{ '-menu js-items-list\">
        <a class=\"dropdown-item\" data-item=\"Partout\" href=\"#\" data-value=\"0\" data-placeholder=\"Que souhaitez-vous trouver ?\" data-icon=\"icon-search\"><i class=\"material-icons\">search</i> Partout</a>
        <div class=\"dropdown-divider\"></div>
        <a class=\"dropdown-item\" data-item=\"Catalogue\" href=\"#\" data-value=\"1\" data-placeholder=\"Nom du produit, référence, etc.\" data-icon=\"icon-book\"><i class=\"material-icons\">store_mall_directory</i> Catalogue</a>
        <a class=\"dropdown-item\" data-item=\"Clients par nom\" href=\"#\" data-value=\"2\" data-placeholder=\"Nom\" data-icon=\"icon-group\"><i class=\"material-icons\">group</i> Clients par nom</a>
        <a class=\"dropdown-item\" data-item=\"Clients par adresse IP\" href=\"#\" data-value=\"6\" data-placeholder=\"123.45.67.89\" data-icon=\"icon-desktop\"><i class=\"material-icons\">desktop_mac</i> Clients par adresse IP</a>
        <a class=\"dropdown-item\" data-item=\"Commandes\" href=\"#\" data-value=\"3\" data-placeholder=\"ID commande\" data-icon=\"icon-credit-card\"><i class=\"material-icons\">shopping_basket</i> Commandes</a>
        <a class=\"dropdown-item\" data-item=\"Factures\" href=\"#\" data-value=\"4\" data-placeholder=\"Numéro de facture\" data-icon=\"icon-book\"><i class=\"material-icons\">book</i> Factures</a>
        <a class=\"dropdown-item\" data-item=\"Paniers\" href=\"#\" data-value=\"5\" data-placeholder=\"ID panier\" data-icon=\"icon-shopping-cart\"><i class=\"material-icons\">shopping_cart</i> Paniers</a>
        <a class=\"dropdown-item\" data-item=\"Modules\" href=\"#\" data-value=\"7\" data-placeholder=\"Nom du module\" data-icon=\"icon-puzzle-piece\"><i class=\"material-icons\">extension</i> Modules</a>
      </div>
      <button class=\"btn btn-primary\" type=\"submit\"><span class=\"d-none\">RECHERCHE</span><i class=\"material-icons\">search</i></button>
    </div>
  </div>
</form>

<script type=\"text/javascript\">
 \$(document).ready(function(){
    \$(\\'#bo_query\\').one(\\'click\\', function() {
    \$(this).closest(\\'form\\').removeClass(\\'collapsed\\');
  ' | raw }}{{ '});
});
</script>
            <button class=\"component-search-cancel d-none\">Annuler</button>
          </div>

          <div class=\"component-search-quickaccess d-none\">
  <p class=\"component-search-title\">Accès rapide</p>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost&amp;token=fd0d13e8606c3aa0c326c11992702f8c\"
       target=\"_blank\"       data-item=\"Blog\"
    >Blog</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders?token=4abc6762ed44956188f556510f9a700f\"
             data-item=\"Commandes\"
    >Commandes</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/../../controle.php?token=4abc6762ed44956188f556510f9a700f\"
       target=\"_blank\"       data-item=\"Contrôle Code Barre\"
    >Contrôle Code Barre</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statsstocksinventaire&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
             data-item=\"Etat des stocks / Inventaire\"
    >Etat des stocks / Inventaire</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats&amp;module=statscheckup&amp;token=99b81e181c80f33f6ebeeb9207b7c271\"
             data-item=\"Évaluation du catalogue\"
    >Évaluation du catalogue</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ets_megamenu&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"Mega Menu\"
    >Mega Menu</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=moduleperso&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-ite' | raw }}{{ 'm=\"Module Perso\"
    >Module Perso</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage?token=4abc6762ed44956188f556510f9a700f\"
             data-item=\"Modules installés\"
    >Modules installés</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules&amp;addcart_rule&amp;token=f8f9e6419a666825ad443054d4da45ab\"
             data-item=\"Nouveau bon de réduction\"
    >Nouveau bon de réduction</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=homecategories&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"Produits phares\"
    >Produits phares</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&amp;configure=ps_imageslider&amp;token=1a74a9290309a098688add3a3b4458a7\"
             data-item=\"SLIDER\"
    >SLIDER</a>
      <a class=\"dropdown-item quick-row-link\"
       href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminFlashSales&amp;token=28209f1f2d55b6c1fc5f31f26c7e9f95\"
             data-item=\"Ventes Flash\"
    >Ventes Flash</a>
    <div class=\"dropdown-divider\"></div>
      <a id=\"quick-add-link\"
      class=\"dropdown-item js-quick-link\"
      href=\"#\"
      data-rand=\"66\"
      data-icon=\"icon-AdminParentPreferences\"
      data-method=\"add\"
      data-url=\"index.php/configure/shop/maintenance\"
      data-post-link=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuickAccesses\"
      data-prompt-text=\"Veuillez nommer ce raccourci :\"
      data-link=\"Maintenance - Liste\"
    >
      <i class=\"material-icons\">add_circle</i>
      Ajouter la page actuelle à l\\'accès rapide
    </a>
    <a id=\"quick-manage-link\" class=\"dropdown-item\" href=\"https://dev.labonnegraine.com/admin1' | raw }}{{ '23/index.php?controller=AdminQuickAccesses\">
    <i class=\"material-icons\">settings</i>
    Gérez vos accès rapides
  </a>
</div>
        </div>

        <div class=\"component-search-background d-none\"></div>
      </div>

              <div class=\"component hide-mobile-sm\" id=\"header-debug-mode-container\">
          <a class=\"link shop-state\"
             id=\"debug-mode\"
             data-toggle=\"pstooltip\"
             data-placement=\"bottom\"
             data-html=\"true\"
             title=\"<p class=&quot;text-left&quot;><strong>Votre boutique est en mode debug.</strong></p><p class=&quot;text-left&quot;>Tous les messages et erreurs PHP sont affichés. Lorsque vous n&#039;en avez plus besoin, &lt;strong&gt;désactivez&lt;/strong&gt; ce mode.</p>\"
             href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/performance/\"
          >
            <i class=\"material-icons\">bug_report</i>
            <span>Mode debug</span>
          </a>
        </div>
      
              <div class=\"component hide-mobile-sm\" id=\"header-maintenance-mode-container\">
          <a class=\"link shop-state\"
             id=\"maintenance-mode\"
             data-toggle=\"pstooltip\"
             data-placement=\"bottom\"
             data-html=\"true\"
             title=\"<p class=&quot;text-left&quot;><strong>Votre boutique est en maintenance.</strong></p><p class=&quot;text-left&quot;>Vos clients et visiteurs ne peuvent y accéder actuellement. &amp;lt;br /&amp;gt; Vous pouvez gérer les paramètres de maintenance dans l&#039;onglet Maintenance de la page Paramètres de la boutique.</p>\" href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\"
          >
            <i class=\"material-icons\">build</i>
            <span>Mode maintenance</span>
          </a>
        </div>
      
      <div class=\"header-right\">
                  <div class=\"component\" id=\"header-shop-list-container\">
              <div class=\"shop-list\">
    <a class=\"link\"' | raw }}{{ ' id=\"header_shopname\" href=\"https://dev.labonnegraine.com/\" target= \"_blank\">
      <i class=\"material-icons\">visibility</i>
      <span>Voir ma boutique</span>
    </a>
  </div>
          </div>
                
        <div class=\"component\" id=\"header-employee-container\">
          <div class=\"dropdown employee-dropdown\">
  <div class=\"rounded-circle person\" data-toggle=\"dropdown\">
    <i class=\"material-icons\">account_circle</i>
  </div>
  <div class=\"dropdown-menu dropdown-menu-right\">
    <div class=\"employee-wrapper-avatar\">
      <div class=\"employee-top\">
        <span class=\"employee-avatar\"><img class=\"avatar rounded-circle\" src=\"https://dev.labonnegraine.com/img/pr/default.jpg\" alt=\"Guillaume\" /></span>
        <span class=\"employee_profile\">Ravi de vous revoir Guillaume</span>
      </div>

      <a class=\"dropdown-item employee-link profile-link\" href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/17/edit\">
      <i class=\"material-icons\">edit</i>
      <span>Votre profil</span>
    </a>
    </div>

    <p class=\"divider\"></p>

    
    <a class=\"dropdown-item employee-link text-center\" id=\"header_logout\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminLogin&amp;logout=1\">
      <i class=\"material-icons d-lg-none\">power_settings_new</i>
      <span>Déconnexion</span>
    </a>
  </div>
</div>
        </div>
              </div>
    </nav>
  </header>

  <nav class=\"nav-bar d-none d-print-none d-md-block\">
  <span class=\"menu-collapse\" data-toggle-url=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/toggle-navigation\">
    <i class=\"material-icons rtl-flip\">chevron_left</i>
    <i class=\"material-icons rtl-flip\">chevron_left</i>
  </span>

  <div class=\"nav-bar-overflow\">
      <div class=\"logo-container\">
          <a id=\"header_logo\" class=\"logo float-left\" href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\"></a>
          <span id=\"shop_' | raw }}{{ 'version\" class=\"header-version\">8.0.1</span>
      </div>

      <ul class=\"main-menu\">
              
                    
                    
          
            <li class=\"link-levelone\" data-submenu=\"1\" id=\"tab-AdminDashboard\">
              <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\" class=\"link\" >
                <i class=\"material-icons\">trending_up</i> <span>Tableau de bord</span>
              </a>
            </li>

          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"2\" id=\"tab-SELL\">
                <span class=\"title\">Vendre</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"3\" id=\"subtab-AdminParentOrders\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDmuListeCommandes\" class=\"link\">
                      <i class=\"material-icons mi-shopping_basket\">shopping_basket</i>
                      <span>
                      Commandes
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-3\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"207\" id=\"subtab-AdminDmuListeCommandes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?c' | raw }}{{ 'ontroller=AdminDmuListeCommandes\" class=\"link\"> Commandes Améliorées
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"4\" id=\"subtab-AdminOrders\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"5\" id=\"subtab-AdminInvoices\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/invoices/\" class=\"link\"> Factures
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"6\" id=\"subtab-AdminSlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/credit-slips/\" class=\"link\"> Avoirs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"7\" id=\"subtab-AdminDeliverySlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/orders/delivery-slips/\" class=\"link\"> ' | raw }}{{ 'Bons de livraison
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"8\" id=\"subtab-AdminCarts\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarts\" class=\"link\"> Paniers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"132\" id=\"subtab-AdminControleCommande\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminControleCommande\" class=\"link\"> Contrôle des commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"135\" id=\"subtab-AdminBox\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminBox\" class=\"link\"> Box
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"204\" id=\"subtab-AdminDPDFrance\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDPDFrance\" class=\"link\"> DPD France livraison
 ' | raw }}{{ '                               </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"205\" id=\"subtab-AdminDPDFranceReturn\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDPDFranceReturn\" class=\"link\"> DPD France retour
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"9\" id=\"subtab-AdminCatalog\">
                    <a href=\"/admin123/index.php/sell/catalog/products\" class=\"link\">
                      <i class=\"material-icons mi-store\">store</i>
                      <span>
                      Catalogue
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-9\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"10\" id=\"subtab-AdminProducts\">
                                <a href=\"/admin123/index.php/sell/catalog/products\" class=\"link\"> Produits
                                </a>
               ' | raw }}{{ '               </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"11\" id=\"subtab-AdminCategories\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/categories\" class=\"link\"> Catégories
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"12\" id=\"subtab-AdminTracking\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/monitoring/\" class=\"link\"> Suivi
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"13\" id=\"subtab-AdminParentAttributesGroups\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminAttributesGroups\" class=\"link\"> Attributs et caractéristiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"16\" id=\"subtab-AdminParentManufacturers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/catalog/brands/\" class=\"link\"> Marques et fournisseurs
                             ' | raw }}{{ '   </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"19\" id=\"subtab-AdminAttachments\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/attachments/\" class=\"link\"> Fichiers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"20\" id=\"subtab-AdminParentCartRules\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCartRules\" class=\"link\"> Réductions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"23\" id=\"subtab-AdminStockManagement\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/stocks/\" class=\"link\"> Stock
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"164\" id=\"subtab-AdminQuantityDiscountRules\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminQuantityDiscountRules\" class=\"link\"> Promotions and discounts
                               ' | raw }}{{ ' </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"176\" id=\"subtab-MajQtyMasse\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=MajQtyMasse\" class=\"link\"> Mise a jour quantites de masse
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"177\" id=\"subtab-MajmsgMasse\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=MajmsgMasse\" class=\"link\"> Mise a jour messages de masse
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"203\" id=\"subtab-FichesEnvoiClientTab\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=FichesEnvoiClientTab\" class=\"link\"> Envoi des fiches aux clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"247\" id=\"subtab-AdminCategoryHeaderMessages\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCategoryHeaderMessage' | raw }}{{ 's\" class=\"link\"> Messages commerciaux
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"24\" id=\"subtab-AdminParentCustomer\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customers/\" class=\"link\">
                      <i class=\"material-icons mi-account_circle\">account_circle</i>
                      <span>
                      Clients
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-24\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"25\" id=\"subtab-AdminCustomers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customers/\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"26\" id=\"subtab-AdminAddresses\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/addresses' | raw }}{{ '/\" class=\"link\"> Adresses
                                </a>
                              </li>

                                                                                                                                        
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"242\" id=\"subtab-AdminLiveCartReminder\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminLiveCartReminder\" class=\"link\"> Relance panier
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"28\" id=\"subtab-AdminParentCustomerThreads\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCustomerThreads\" class=\"link\">
                      <i class=\"material-icons mi-chat\">chat</i>
                      <span>
                      SAV
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-28\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"29\" id=\"subtab-AdminCustomerThreads\">
                         ' | raw }}{{ '       <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCustomerThreads\" class=\"link\"> SAV
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"30\" id=\"subtab-AdminOrderMessage\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/sell/customer-service/order-messages/\" class=\"link\"> Messages prédéfinis
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"31\" id=\"subtab-AdminReturn\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminReturn\" class=\"link\"> Retours produits
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"32\" id=\"subtab-AdminStats\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStats\" class=\"link\">
                      <i class=\"material-icons mi-assessment\">assessment</i>
                      <span>
                      Statistiques
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                              ' | raw }}{{ '      keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"37\" id=\"tab-IMPROVE\">
                <span class=\"title\">Personnaliser</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"38\" id=\"subtab-AdminParentModulesSf\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Modules
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-38\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"39\" id=\"subtab-AdminModulesSf\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/modules/manage\" class=\"link\"> Gestionnaire de modules 
                                </a>
                              </li>

                                                                                                                                        
   ' | raw }}{{ '                           
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"208\" id=\"subtab-AdminStoreCommander\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminStoreCommander\" class=\"link\"> Store Commander
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"43\" id=\"subtab-AdminParentThemes\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/themes/\" class=\"link\">
                      <i class=\"material-icons mi-desktop_mac\">desktop_mac</i>
                      <span>
                      Apparence
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-43\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"124\" id=\"subtab-AdminThemesParent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/themes/\" class=\"link\"> Thème et logo
                                </a>
                              </li>

                                                   ' | raw }}{{ '                               
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"45\" id=\"subtab-AdminParentMailTheme\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/mail_theme/\" class=\"link\"> Thème d&#039;e-mail
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"47\" id=\"subtab-AdminCmsContent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/cms-pages/\" class=\"link\"> Pages
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"48\" id=\"subtab-AdminModulesPositions\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/design/modules/positions/\" class=\"link\"> Positions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"49\" id=\"subtab-AdminImages\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminImages\" class=\"link\"> Images
                                </a>
                              </li>

                                                              ' | raw }}{{ '                    
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"117\" id=\"subtab-AdminLinkWidget\">
                                <a href=\"/admin123/index.php/modules/link-widget/list\" class=\"link\"> Liste de liens
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"233\" id=\"subtab-AdminParentCEContent\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEThemes\" class=\"link\">
                      <i class=\"material-icons mi-ce\">ce</i>
                      <span>
                      Creative Elements
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-233\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"234\" id=\"subtab-AdminCEThemes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEThemes\" class=\"link\"> Constructeur de thème
                                </a>
                              </li>

                                                                ' | raw }}{{ '                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"235\" id=\"subtab-AdminCEContent\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEContent\" class=\"link\"> Contenu n’importe où
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"236\" id=\"subtab-AdminCETemplates\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCETemplates\" class=\"link\"> Modèles enregistrés
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"237\" id=\"subtab-AdminParentCEFonts\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCEFonts\" class=\"link\"> Polices &amp; Icônes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"240\" id=\"subtab-AdminCESettings\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCESettings\" class=\"link\"> Réglages
                                </a>
                              </li>

                                              ' | raw }}{{ '                                                                                      </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"50\" id=\"subtab-AdminParentShipping\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarriers\" class=\"link\">
                      <i class=\"material-icons mi-local_shipping\">local_shipping</i>
                      <span>
                      Livraison
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-50\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"51\" id=\"subtab-AdminCarriers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCarriers\" class=\"link\"> Transporteurs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"52\" id=\"subtab-AdminShipping\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/shipping/preferences/\" class=\"link\"> Préférences
                                </a' | raw }}{{ '>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"170\" id=\"subtab-AdminColissimoDashboard\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoDashboard\" class=\"link\"> Colissimo - Tableau de bord
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"171\" id=\"subtab-AdminColissimoAffranchissement\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoAffranchissement\" class=\"link\"> Colissimo - Affranchissement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"172\" id=\"subtab-AdminColissimoDepositSlip\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoDepositSlip\" class=\"link\"> Colissimo - Bordereaux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"173\" id=\"subtab-AdminColissimoColiship\">
                                <a href=\"https://dev.labonnegraine.com/adm' | raw }}{{ 'in123/index.php?controller=AdminColissimoColiship\" class=\"link\"> Colissimo - Coliship
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"174\" id=\"subtab-AdminColissimoCustomsDocuments\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminColissimoCustomsDocuments\" class=\"link\"> Colissimo - Documents
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"53\" id=\"subtab-AdminParentPayment\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/payment_methods\" class=\"link\">
                      <i class=\"material-icons mi-payment\">payment</i>
                      <span>
                      Paiement
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-53\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"54\" id=\"subtab-AdminPayment\">' | raw }}{{ '
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/payment_methods\" class=\"link\"> Moyens de paiement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"55\" id=\"subtab-AdminPaymentPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/payment/preferences\" class=\"link\"> Préférences
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"210\" id=\"subtab-AdminPaymentFee\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPaymentFee\" class=\"link\"> Les frais de paiement
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"56\" id=\"subtab-AdminInternational\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/localization/\" class=\"link\">
                      <i class=\"material-icons mi-language\">language</i>
                      <span>
                      International
                      </span>
                                                    <i class=\"material-icons sub-t' | raw }}{{ 'abs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-56\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"57\" id=\"subtab-AdminParentLocalization\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/localization/\" class=\"link\"> Localisation
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"62\" id=\"subtab-AdminParentCountries\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/zones/\" class=\"link\"> Zones géographiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"66\" id=\"subtab-AdminParentTaxes\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/taxes/\" class=\"link\"> Taxes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                     ' | raw }}{{ '         <li class=\"link-leveltwo\" data-submenu=\"69\" id=\"subtab-AdminTranslations\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/improve/international/translations/settings\" class=\"link\"> Traductions
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                                            
          
                      
                                          
                    
          
            <li class=\"category-title link-active\" data-submenu=\"70\" id=\"tab-CONFIGURE\">
                <span class=\"title\">Configurer</span>
            </li>

                              
                  
                                                      
                                                          
                  <li class=\"link-levelone has_submenu link-active open ul-open\" data-submenu=\"71\" id=\"subtab-ShopParameters\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" class=\"link\">
                      <i class=\"material-icons mi-settings\">settings</i>
                      <span>
                      Paramètres de la boutique
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_up
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-71\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo link-active\" data-su' | raw }}{{ 'bmenu=\"72\" id=\"subtab-AdminParentPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" class=\"link\"> Paramètres généraux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"75\" id=\"subtab-AdminParentOrderPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/order-preferences/\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"78\" id=\"subtab-AdminPPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/product-preferences/\" class=\"link\"> Produits
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"79\" id=\"subtab-AdminParentCustomerPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/customer-preferences/\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
   ' | raw }}{{ '                           <li class=\"link-leveltwo\" data-submenu=\"83\" id=\"subtab-AdminParentStores\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/contacts/\" class=\"link\"> Contact
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"86\" id=\"subtab-AdminParentMeta\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/seo-urls/\" class=\"link\"> Trafic et SEO
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"89\" id=\"subtab-AdminParentSearchConf\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminSearchConf\" class=\"link\"> Rechercher
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"92\" id=\"subtab-AdminAdvancedParameters\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/system-information/\" class=\"link\">
                      <i class=\"material-icons mi-settings_applications\">settings_applications</i>
                      <span>
                      Paramètres avancés
     ' | raw }}{{ '                 </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-92\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"93\" id=\"subtab-AdminInformation\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/system-information/\" class=\"link\"> Informations
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"94\" id=\"subtab-AdminPerformance\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/performance/\" class=\"link\"> Performances
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"95\" id=\"subtab-AdminAdminPreferences\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/administration/\" class=\"link\"> Administration
                                </a>
                              </li>

                                                                                  ' | raw }}{{ '
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"96\" id=\"subtab-AdminEmails\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/emails/\" class=\"link\"> E-mail
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"97\" id=\"subtab-AdminImport\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/import/\" class=\"link\"> Importer
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"98\" id=\"subtab-AdminParentEmployees\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/employees/\" class=\"link\"> Équipe
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"102\" id=\"subtab-AdminParentRequestSql\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/sql-requests/\" class=\"link\"> Base de données
                                </a>
                              </li>

                                                                                  
         ' | raw }}{{ '                     
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"105\" id=\"subtab-AdminLogs\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/logs/\" class=\"link\"> Logs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"106\" id=\"subtab-AdminWebservice\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/webservice-keys/\" class=\"link\"> Webservice
                                </a>
                              </li>

                                                                                                                                                                                              
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"109\" id=\"subtab-AdminFeatureFlag\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/advanced/feature-flags/\" class=\"link\"> Fonctionnalités nouvelles et expérimentales
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"110\" id=\"subtab-AdminParentSecurity\">
                                <a href=\"/admin123/index.php/configure/advanced/security/\" class=\"link\"> Sécurité
                                </a>
                      ' | raw }}{{ '        </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"206\" id=\"subtab-AdminCdcGoogletagmanagerOrders\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminCdcGoogletagmanagerOrders\" class=\"link\"> GTM Orders
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"142\" id=\"tab-AdminUTMTab\">
                <span class=\"title\">UTM</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"143\" id=\"subtab-AdminUtmStats\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminUtmStats\" class=\"link\">
                      <i class=\"material-icons mi-settings\">settings</i>
                      <span>
                      StatsUTM
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"144\" id=\"tab-' | raw }}{{ 'PsaffiliateAdmin\">
                <span class=\"title\">PS Affiliate</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"145\" id=\"subtab-AdminPsaffiliateAdmin\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateAdmin\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Tableau de bord
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"146\" id=\"subtab-AdminPsaffiliateConfiguration\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateConfiguration\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Paramètres
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
              ' | raw }}{{ '    <li class=\"link-levelone\" data-submenu=\"147\" id=\"subtab-AdminPsaffiliateAffiliates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateAffiliates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Affiliates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"148\" id=\"subtab-AdminPsaffiliateCustomFields\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCustomFields\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Affiliates Custom Fields
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"149\" id=\"subtab-AdminPsaffiliatePayments\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliatePayments\" class=\"link\">
' | raw }}{{ '                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Payments
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"150\" id=\"subtab-AdminPsaffiliatePaymentMethods\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliatePaymentMethods\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Moyens de paiement
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"151\" id=\"subtab-AdminPsaffiliateBanners\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateBanners\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Banners
                      </span>
                                                    <i class=\"' | raw }}{{ 'material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"152\" id=\"subtab-AdminPsaffiliateTexts\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateTexts\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Text Ads
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"153\" id=\"subtab-AdminPsaffiliateRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      General Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
        ' | raw }}{{ '                                </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"154\" id=\"subtab-AdminPsaffiliateCategoryRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCategoryRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Category Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"155\" id=\"subtab-AdminPsaffiliateProductRates\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateProductRates\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Product Commission Rates
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                 ' | raw }}{{ ' <li class=\"link-levelone\" data-submenu=\"156\" id=\"subtab-AdminPsaffiliateTraffic\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateTraffic\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Trafic
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"157\" id=\"subtab-AdminPsaffiliateSales\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateSales\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Sales
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"158\" id=\"subtab-AdminPsaffiliateCampaigns\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateCampaigns\" class=\"link\">
                      <i class=\"material-ico' | raw }}{{ 'ns mi-extension\">extension</i>
                      <span>
                      Campaigns
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"159\" id=\"subtab-AdminPsaffiliateStatistics\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminPsaffiliateStatistics\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Statistics
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"182\" id=\"tab-AdminEtsAm\">
                <span class=\"title\">Programmes de marketing</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"183\" id=\"subtab-AdminEtsAmDashboard\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmDashboard\" class=\"link\">
    ' | raw }}{{ '                  <i class=\"material-icons mi-eam_show_chart fa fa-line-chart \">eam_show_chart fa fa-line-chart </i>
                      <span>
                      Tableau de bord
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"184\" id=\"subtab-AdminEtsAmMarketing\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmLoyalty\" class=\"link\">
                      <i class=\"material-icons mi-marketing\">marketing</i>
                      <span>
                      Marketing program
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-184\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"185\" id=\"subtab-AdminEtsAmLoyalty\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmLoyalty\" class=\"link\"> Programme de fidélité 
                                </a>
                              </li>

                  ' | raw }}{{ '                                                                
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"186\" id=\"subtab-AdminEtsAmRS\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRS\" class=\"link\"> Programme de parrainage 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"187\" id=\"subtab-AdminEtsAmAffiliate\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmAffiliate\" class=\"link\"> Programme d&#039;affiliation 
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"188\" id=\"subtab-AdminEtsAmRewards\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRU\" class=\"link\">
                      <i class=\"material-icons mi-rewards\">rewards</i>
                      <span>
                      Rewards
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                          ' | raw }}{{ '    <ul id=\"collapse-188\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"189\" id=\"subtab-AdminEtsAmRU\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRU\" class=\"link\"> Utilisation de la récompense
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"190\" id=\"subtab-AdminEtsAmRewardHistory\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmRewardHistory\" class=\"link\"> Récompense l&#039;histoire
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"191\" id=\"subtab-AdminEtsAmWithdrawals\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmWithdrawals\" class=\"link\"> Retrait
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"192\" id=\"subtab-AdminEtsAmCustomers\">
                    <a href=\"https://dev.labonneg' | raw }}{{ 'raine.com/admin123/index.php?controller=AdminEtsAmApp\" class=\"link\">
                      <i class=\"material-icons mi-customer\">customer</i>
                      <span>
                      Clients
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-192\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"193\" id=\"subtab-AdminEtsAmApp\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmApp\" class=\"link\"> Candidatures
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo\" data-submenu=\"194\" id=\"subtab-AdminEtsAmUsers\">
                                <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmUsers\" class=\"link\"> Utilisateurs
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"195\" id=\"subtab-AdminEtsAmBackup\">
                    <a href=\"https://dev.labon' | raw }}{{ 'negraine.com/admin123/index.php?controller=AdminEtsAmBackup\" class=\"link\">
                      <i class=\"material-icons mi-swap_horiz\">swap_horiz</i>
                      <span>
                      Restauration / Sauvegarde
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"196\" id=\"subtab-AdminEtsAmCronjob\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmCronjob\" class=\"link\">
                      <i class=\"material-icons mi-tasks\">tasks</i>
                      <span>
                      Cronjob
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"197\" id=\"subtab-AdminEtsAmGeneral\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminEtsAmGeneral\" class=\"link\">
                      <i class=\"material-icons mi-cogs\">cogs</i>
                      <span>
                      Réglages généraux
                      </span>
                      ' | raw }}{{ '                              <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title\" data-submenu=\"222\" id=\"tab-AdminYbcBlog\">
                <span class=\"title\">Blog</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"223\" id=\"subtab-AdminYbcBlogPost\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPost\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminPriceRule\">icon icon-AdminPriceRule</i>
                      <span>
                      Articles
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"224\" id=\"subtab-AdminYbcBlogCategory\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogCategory\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminCatalog\">icon icon-AdminCatalog</i>
                      <span>
                      Cat' | raw }}{{ 'égories
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"225\" id=\"subtab-AdminYbcBlogComment\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogComment\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-comments\">icon icon-comments</i>
                      <span>
                      Commentaires
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"226\" id=\"subtab-AdminYbcBlogPolls\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogPolls\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-polls\">icon icon-polls</i>
                      <span>
                      Sondages
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
               ' | raw }}{{ '                                             </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"227\" id=\"subtab-AdminYbcBlogSlider\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogSlider\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminParentModules\">icon icon-AdminParentModules</i>
                      <span>
                      Glissière
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"228\" id=\"subtab-AdminYbcBlogGallery\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogGallery\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminDashboard\">icon icon-AdminDashboard</i>
                      <span>
                      Galerie photo
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
 ' | raw }}{{ '                 
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"229\" id=\"subtab-AdminYbcBlogAuthor\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogAuthor\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-user\">icon icon-user</i>
                      <span>
                      Auteurs
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"230\" id=\"subtab-AdminYbcBlogStatistics\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogStatistics\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-chart\">icon icon-chart</i>
                      <span>
                      Statistiques
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"231\" id=\"subtab-AdminYbcBlogBackUp\">
                    <a href=\"https://dev.labonnegr' | raw }}{{ 'aine.com/admin123/index.php?controller=AdminYbcBlogBackUp\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-exchange\">icon icon-exchange</i>
                      <span>
                      Importer / Exporter
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"232\" id=\"subtab-AdminYbcBlogSetting\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminYbcBlogSetting\" class=\"link\">
                      <i class=\"material-icons mi-icon icon-AdminAdmin\">icon icon-AdminAdmin</i>
                      <span>
                      Paramètres globaux
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                  </ul>
  </div>
  
</nav>


<div class=\"header-toolbar d-print-none\">
    
  <div class=\"container-fluid\">

    
      <nav aria-label=\"Breadcrumb\">
        <ol class=\"breadcrumb\">
                      <li class=\"breadcrumb-item\">Paramètres généraux</li>
          
                      <li class=\"breadcrumb-item active\">
              <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\" ari' | raw }}{{ 'a-current=\"page\">Maintenance</a>
            </li>
                  </ol>
      </nav>
    

    <div class=\"title-row\">
      
          <h1 class=\"title\">
            Maintenance          </h1>
      

      
        <div class=\"toolbar-icons\">
          <div class=\"wrapper\">
            
                        
            
                              <a class=\"btn btn-outline-secondary btn-help btn-sidebar\" href=\"#\"
                   title=\"Aide\"
                   data-toggle=\"sidebar\"
                   data-target=\"#right-sidebar\"
                   data-url=\"/admin123/index.php/common/sidebar/https%253A%252F%252Fhelp.prestashop-project.org%252Ffr%252Fdoc%252FAdminMaintenance%253Fversion%253D8.0.1%2526country%253Dfr/Aide\"
                   id=\"product_form_open_help\"
                >
                  Aide
                </a>
                                    </div>
        </div>

      
    </div>
  </div>

  
      <div class=\"page-head-tabs\" id=\"head_tabs\">
      <ul class=\"nav nav-pills\">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ' | raw }}{{ '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <li class=\"nav-item\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/preferences/preferences\" id=\"subtab-AdminPreferences\" class=\"nav-link tab \" data-submenu=\"73\">
                      Paramètres généraux
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                <li class=\"nav-item\">
                    <a href=\"https://dev.labonnegraine.com/admin123/index.php/configure/shop/maintenance/\" id=\"subtab-AdminMaintenance\" class=\"nav-link tab active current\" data-submenu=\"74\">
                      Maintenance
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                                                                                                                                                                                                                                                                                              ' | raw }}{{ '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </ul>
    </div>
  
  <div class=\"btn-floating\">
    <button class=\"btn btn-primary collapsed\" data-toggle=\"collapse\" data-target=\".btn-floating-container\" aria-expanded=\"false\">
      <i class=\"material-icons\">add</i>
    </button>
    <div class=\"btn-floating-container collapse\">
      <div class=\"btn-floating-menu\">
        
        
                              <a class=\"btn btn-floating-item btn-help btn-sidebar\" href=\"#\"
               title=\"Aide\"
               data-toggle=\"sidebar\"
               data-target=\"#right-sidebar\"
               data-url=\"/admin123/index.php/common/sidebar/https%253A%252F%252Fhelp.prestashop-project.org%252Ffr%252Fdoc%252FAdminMaintenance%253Fversion%253D8.0.1%2526country%253Dfr/Aide\"
            >
              Aide
            </a>
                        </div>
    </div>
  </div>
  
</div>

<div id=\"main-div\">
          
      <div class=\"content-div  with-tabs\">

        

                                                 ' | raw }}{{ '       
        <div id=\"ajax_confirmation\" class=\"alert alert-success\" style=\"display: none;\"></div>
<div id=\"content-message-box\"></div>


  ' | raw }}{% block content_header %}{% endblock %}{% block content %}{% endblock %}{% block content_footer %}{% endblock %}{% block sidebar_right %}{% endblock %}{{ '

        

      </div>
    </div>

  <div id=\"non-responsive\" class=\"js-non-responsive\">
  <h1>Oh non !</h1>
  <p class=\"mt-3\">
    La version mobile de cette page n\\'est pas encore disponible.
  </p>
  <p class=\"mt-2\">
    Cette page n\\'est pas encore disponible sur mobile, merci de la consulter sur ordinateur.
  </p>
  <p class=\"mt-2\">
    Merci.
  </p>
  <a href=\"https://dev.labonnegraine.com/admin123/index.php?controller=AdminDashboard\" class=\"btn btn-primary py-1 mt-3\">
    <i class=\"material-icons rtl-flip\">arrow_back</i>
    Précédent
  </a>
</div>
  <div class=\"mobile-layer\"></div>

      <div id=\"footer\" class=\"bootstrap\">
    <script type=\"text/javascript\">
var link_ajax=\\'https://dev.labonnegraine.com/admin123/index.php?controller=AdminModules&configure=ybc_blog&tab_module=front_office_features&module_name=ybc_blog\\';
\$(document).ready(function(){
    \$.ajax({
        url: link_ajax,
        data: \\'action=getCountMessageYbcBlog\\',
        type: \\'post\\',
        dataType: \\'json\\',                
        success: function(json){ 
            if(parseInt(json.count) >0)
            {
                if(\$(\\'#subtab-AdminYbcBlogComment span\\').length)
                    \$(\\'#subtab-AdminYbcBlogComment span\\').append(\\'<span class=\"count_messages \">\\'+json.count+\\'</span>\\'); 
                else
                    \$(\\'#subtab-AdminYbcBlogComment a\\').append(\\'<span class=\"count_messages \">\\'+json.count+\\'</span>\\');
            }
            else
            {
                if(\$(\\'#subtab-AdminYbcBlogComment span\\').length)
                    \$(\\'#subtab-AdminYbcBlogComment span\\').append(\\'<span class=\"count_messages hide\">\\'+json.count+\\'</span>\\'); 
                else
                    \$(\\'#subtab-AdminYbcBlogComment a\\').append(\\'<span class=\"count_messages hide\">\\'+json.count+\\'</span>\\');
            }
                                                              
        },
    });
});
</script>
</div>
  

      <div class=\"bootstrap\">
      
    </div>
  
' | raw }}{% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}</body>{{ '
</html>' | raw }}", "__string_template__73ecd62107498bcd0e80e8a2f8a1b7d6b4551b5e7cc6059d7dd53493f3b00269", "");
    }
}

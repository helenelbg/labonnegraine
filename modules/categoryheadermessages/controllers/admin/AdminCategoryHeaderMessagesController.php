<?php
/**
 * Contrôleur d'administration pour le module Messages Commerciaux
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCategoryHeaderMessagesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'category_header_message';
        $this->className = 'CategoryHeaderMessage';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->explicitSelect = false;
        $this->allow_export = true;
        $this->delete = true;
        $this->context = Context::getContext();
        
        // Solution simple: forcer l'identifiant primaire explicitement
        $this->identifier = 'id_message';
        
        //$this->_select = 'SQL_CALC_FOUND_ROWS a.`id_message`, `title`, `date_start`, `date_end`, `active`';
        //$this->_select = 'a.`id_message`, a.`active`, a.`date_start`, a.`date_end`, b.`title`'; 

        // Définition des champs de la liste
        $this->fields_list = [
            'id_message' => [ // Assurez-vous d'avoir l'ID dans la liste
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            /*'id_message' => [
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],*/
            'title' => [
                'title' => 'Titre'
            ],
            'date_start' => [
                'title' => 'Date de début',
                'type' => 'datetime'
            ],
            'date_end' => [
                'title' => 'Date de fin',
                'type' => 'datetime'
            ],
            'active' => [
                'title' => 'Actif',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => 'Supprimer la sélection',
                'confirm' => 'Voulez-vous supprimer les éléments sélectionnés?',
                'icon' => 'icon-trash'
            ]
        ];
        
        parent::__construct();
    }

       /* $this->bulk_actions = [
            'delete' => [
                'text' => 'Supprimer la sélection',
                'confirm' => 'Voulez-vous supprimer les éléments sélectionnés?',
                'icon' => 'icon-trash'
            ]
        ];

        parent::__construct();
    }*/

    /**
     * Affichage du formulaire d'ajout/édition
     */
    public function renderForm()
    {
        // Si on utilise l'ID explicitement
        if (Tools::isSubmit('id_message')) {
            $this->object = new $this->className(Tools::getValue('id_message'));
        } else {
            // Méthode standard
            if (!$this->loadObject(true)) {
                return;
            }
        }
    
        // Récupérer manuellement les données multilingues
        if ($this->object->id_message) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'category_header_message_lang` 
                       WHERE `id_message` = '.(int)$this->object->id_message.' 
                       AND `id_lang` = '.(int)$language['id_lang'];
                $data = Db::getInstance()->getRow($sql);
                
                if ($data) {
                    $this->object->title[$language['id_lang']] = $data['title'];
                    $this->object->content[$language['id_lang']] = $data['content'];
                    $this->object->cta_text[$language['id_lang']] = $data['cta_text'];
                    $this->object->cta_link[$language['id_lang']] = $data['cta_link'];
                }
            }
        }
    
        // Liste des types de messages
        $message_types = [
            'produit_phare' => $this->trans('Produit phare', [], 'Admin.Global'),
            'promo_moment' => $this->trans('Promo du moment', [], 'Admin.Global'),
            'reduction_lot' => $this->trans('Réduction par lot', [], 'Admin.Global'),
            'accessoires' => $this->trans('Accessoires', [], 'Admin.Global'),
            'offre_eco' => $this->trans('L\'offre eco', [], 'Admin.Global'),
        ];
    
        // Préparation des options pour le champ type
        $type_options = [];
        foreach ($message_types as $key => $label) {
            $type_options[] = [
                'id' => $key,
                'name' => $label,
                'value' => $key
            ];
        }
    
        // Initialisation des variables pour le produit et la déclinaison sélectionnés
        $selected_product = null;
        $combinations = [];
        $selected_combination = null;
    
        // Si un produit est déjà sélectionné
        if ($this->object->id_product) {
            // Récupérer les informations du produit
            $product = new Product((int)$this->object->id_product, false, $this->context->language->id);
            if (Validate::isLoadedObject($product)) {
                $selected_product = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'reference' => $product->reference
                ];
    
                // Récupérer les déclinaisons du produit
                $attrs = $product->getAttributesResume($this->context->language->id);
                if (is_array($attrs)) {
                    foreach ($attrs as $attr) {
                        $combinations[] = [
                            'id' => $attr['id_product_attribute'],
                            'name' => $attr['attribute_designation']
                        ];
                    }
                }
    
                // Déclinaison sélectionnée
                if ($this->object->id_product_attribute) {
                    $selected_combination = $this->object->id_product_attribute;
                }
            }
        }
    
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Message commercial', [], 'Admin.Global'),
                'icon' => 'icon-comment'
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Titre', [], 'Admin.Global'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Type de message', [], 'Admin.Global'),
                    'name' => 'type',
                    'options' => [
                        'query' => $type_options,
                        'id' => 'id',
                        'name' => 'name'
                    ],
                    'required' => true,
                    'hint' => $this->trans('Choisissez le type de message commercial', [], 'Admin.Global')
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Contenu', [], 'Admin.Global'),
                    'name' => 'content',
                    'lang' => true,
                    'autoload_rte' => true,
                    'rows' => 5,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Texte CTA', [], 'Admin.Global'),
                    'name' => 'cta_text',
                    'lang' => true,
                    'hint' => $this->trans('Texte du bouton d\'appel à l\'action', [], 'Admin.Global')
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Lien CTA', [], 'Admin.Global'),
                    'name' => 'cta_link',
                    'lang' => true,
                    'hint' => $this->trans('Lien du bouton d\'appel à l\'action', [], 'Admin.Global')
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->trans('Date de début', [], 'Admin.Global'),
                    'name' => 'date_start',
                    'required' => true
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->trans('Date de fin', [], 'Admin.Global'),
                    'name' => 'date_end',
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Produit associé', [], 'Admin.Global'),
                    'name' => 'product_search',
                    'class' => 'product-search-input',
                    'desc' => $this->trans('Commencez à taper pour rechercher un produit', [], 'Admin.Global'),
                    'suffix' => '<button type="button" class="btn btn-primary search-product-button"><i class="icon-search"></i> '.$this->trans('Rechercher', [], 'Admin.Global').'</button>',
                    'hint' => $this->trans('Choisissez un produit à mettre en avant avec ce message', [], 'Admin.Global')
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_product'
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Déclinaison du produit', [], 'Admin.Global'),
                    'name' => 'id_product_attribute',
                    'options' => [
                        'query' => $combinations,
                        'id' => 'id',
                        'name' => 'name'
                    ],
                    'class' => '',
                    'selected_value' => $selected_combination,
                    'hint' => $this->trans('Choisissez une déclinaison spécifique (optionnel)', [], 'Admin.Global')
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Image', [], 'Admin.Global'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $this->object->getImage() ? $this->context->shop->getBaseURL().'img/chm/'.$this->object->getImage() : false,
                    'size' => 1,
                    'hint' => $this->trans('Image du message commercial', [], 'Admin.Global')
                ],
                [
                    'type' => 'categories',
                    'label' => $this->trans('Catégories', [], 'Admin.Global'),
                    'name' => 'categoryBox',
                    'tree' => [
                        'root_category' => 2,
                        'id' => 'categories-tree',
                        'use_checkbox' => true,
                        'use_search' => true,
                        'selected_categories' => $this->getSelectedCategories(),
                    ],
                    'hint' => $this->trans('Choisissez les catégories où ce message sera affiché', [], 'Admin.Global')
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Actif', [], 'Admin.Global'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Oui', [], 'Admin.Global')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Non', [], 'Admin.Global')
                        ]
                    ]
                ]
            ],
            'submit' => [
                'title' => $this->trans('Enregistrer', [], 'Admin.Actions')
            ],
            'buttons' => [
                'save-and-stay' => [
                    'title' => $this->trans('Enregistrer et rester', [], 'Admin.Actions'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ]
            ]
        ];
       /* $this->fields_form['input'][] = [
    'type' => 'text',
    'label' => $this->trans('Produit associé', [], 'Admin.Global'),
    'name' => 'product_search',
    'class' => 'product-search-input',
    'desc' => $this->trans('Commencez à taper pour rechercher un produit', [], 'Admin.Global'),
    'suffix' => '<button type="button" class="btn btn-primary search-product-button"><i class="icon-search"></i> '.$this->trans('Rechercher', [], 'Admin.Global').'</button>',
    'hint' => $this->trans('Choisissez un produit à mettre en avant avec ce message', [], 'Admin.Global')
];*/

        if ($selected_product) {
            $this->fields_value['product_search'] = $selected_product['name'] . ' (Ref: ' . $selected_product['reference'] . ')';
        }
    
        // Ajouter le JavaScript nécessaire pour gérer le champ de déclinaison
        $this->addJS($this->module->getPathUri().'views/js/product-combinations.js');
        
        return parent::renderForm();
    }

    /**
 * Recherche de produits via AJAX
 */
public function ajaxProcessSearchProducts()
{
    // Ajouter un log pour déboguer
    PrestaShopLogger::addLog('AdminCategoryHeaderMessages: recherche de produits appelée');
    
    $query = Tools::getValue('q', '');
    $limit = (int)Tools::getValue('limit', 20);
    $results = [];
    
    if (!empty($query) && strlen($query) >= 3) {
        // Rechercher dans les noms de produits et références
        $sql = 'SELECT p.id_product, pl.name, p.reference, p.active
                FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl 
                    ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ')
                WHERE (pl.name LIKE "%' . pSQL($query) . '%" 
                    OR p.reference LIKE "%' . pSQL($query) . '%")
                    AND p.active = 1
                ORDER BY pl.name ASC
                LIMIT ' . (int)$limit;
        
        $products = Db::getInstance()->executeS($sql);
        
        if (!empty($products)) {
            foreach ($products as $product) {
                $results[] = [
                    'id' => (int)$product['id_product'],
                    'name' => $product['name'],
                    'reference' => $product['reference'] ?? '',
                    'image' => $this->getProductImageUrl($product['id_product'])
                ];
            }
        }
    }
    
    // Log du nombre de résultats trouvés
    PrestaShopLogger::addLog('AdminCategoryHeaderMessages: ' . count($results) . ' produits trouvés pour la requête: ' . $query);
    
    header('Content-Type: application/json');
    die(json_encode($results));
}

/**
 * Récupérer l'URL de l'image par défaut d'un produit
 */
private function getProductImageUrl($id_product)
{
    $id_product = (int)$id_product;
    if (!$id_product) {
        return null;
    }
    
    // Obtenir directement l'ID de l'image par défaut
    $id_image = Db::getInstance()->getValue('
        SELECT i.id_image
        FROM ' . _DB_PREFIX_ . 'image i
        WHERE i.id_product = ' . $id_product . '
        AND i.cover = 1
        ORDER BY i.position ASC'
    );
    
    // Si aucune image de couverture, prendre la première
    if (!$id_image) {
        $id_image = Db::getInstance()->getValue('
            SELECT i.id_image
            FROM ' . _DB_PREFIX_ . 'image i
            WHERE i.id_product = ' . $id_product . '
            ORDER BY i.position ASC'
        );
    }
    
    if ($id_image) {
        // Obtenir le link_rewrite du produit
        $link_rewrite = Db::getInstance()->getValue('
            SELECT pl.link_rewrite
            FROM ' . _DB_PREFIX_ . 'product_lang pl
            WHERE pl.id_product = ' . $id_product . '
            AND pl.id_lang = ' . (int)$this->context->language->id
        );
        
        $link = new Link();
        return $link->getImageLink($link_rewrite ?: 'product', (int)$id_image, 'small_default');
    }
    
    return null;
}

public function setMedia($isNewTheme = false)
{
    parent::setMedia($isNewTheme);
    
    // Ajouter les scripts et styles spécifiques
    $this->addJS([
        _PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js',
        __PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/jquery.iframe-transport.js',
        __PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/jquery.fileupload.js',
        __PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/jquery.fileupload-process.js',
        __PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/jquery.fileupload-validate.js'
    ]);
    
    // Générer un numéro de version aléatoire pour éviter la mise en cache du JS
    $random_version = '?v=' . rand(100, 999999);
    
    // Ajouter les scripts du module avec le numéro de version
    $this->addJS($this->module->getPathUri() . 'views/js/admin.js' . $random_version);
    $this->addCSS($this->module->getPathUri() . 'views/css/admin.css' . $random_version);
    
    // Ajouter des variables JS pour le débogage
    Media::addJsDef([
        'categoryHeaderMessages' => [
            'deleteConfirmation' => $this->trans('Êtes-vous sûr de vouloir supprimer ce message?', [], 'Admin.Global'),
            'searchUrl' => $this->context->link->getAdminLink('AdminCategoryHeaderMessages', true, [], ['ajax' => 1, 'action' => 'SearchProducts']),
            'combinationsUrl' => $this->context->link->getAdminLink('AdminCategoryHeaderMessages', true, [], ['ajax' => 1, 'action' => 'GetCombinations']),
            'token' => $this->token
        ]
    ]);
}

public function ajaxProcessGetCombinations()
{
    // Ajouter un log détaillé pour le débogage
    PrestaShopLogger::addLog('AdminCategoryHeaderMessages: récupération des déclinaisons appelée avec POST: ' . json_encode($_POST) . ', GET: ' . json_encode($_GET), 1);
    
    // Récupérer l'ID du produit (vérifier à la fois POST et GET)
    $id_product = (int)(Tools::getValue('id_product') ?? 0);
    
    if (!$id_product) {
        // Vérifier si l'ID est dans le corps de la requête JSON
        $json = file_get_contents('php://input');
        if ($json) {
            $data = json_decode($json, true);
            $id_product = (int)($data['id_product'] ?? 0);
        }
    }
    
    // Log du produit trouvé
    PrestaShopLogger::addLog('AdminCategoryHeaderMessages: id_product extrait: ' . $id_product, 1);
    
    $result = [];
    
    if ($id_product > 0) {
        try {
            // Ajouter l'option "Aucune déclinaison" par défaut
            $result[] = [
                'id' => 0,
                'name' => $this->trans('Produit par défaut (aucune déclinaison)', [], 'Admin.Global')
            ];
            
            // Récupérer les combinaisons directement depuis la base de données
            $sql = 'SELECT pa.id_product_attribute, GROUP_CONCAT(CONCAT(agl.name, ": ", al.name) SEPARATOR " - ") as attribute_name
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac 
                    ON pa.id_product_attribute = pac.id_product_attribute
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute a 
                    ON a.id_attribute = pac.id_attribute
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al 
                    ON (a.id_attribute = al.id_attribute AND al.id_lang = ' . (int)$this->context->language->id . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute_group ag 
                    ON ag.id_attribute_group = a.id_attribute_group
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl 
                    ON (ag.id_attribute_group = agl.id_attribute_group AND agl.id_lang = ' . (int)$this->context->language->id . ')
                WHERE pa.id_product = ' . $id_product . '
                GROUP BY pa.id_product_attribute
                ORDER BY pa.id_product_attribute ASC';
            
            $combinations = Db::getInstance()->executeS($sql);
            
            if (!empty($combinations)) {
                foreach ($combinations as $combination) {
                    $result[] = [
                        'id' => (int)$combination['id_product_attribute'],
                        'name' => $combination['attribute_name']
                    ];
                }
            }
            
            // Log du nombre de déclinaisons trouvées
            PrestaShopLogger::addLog('AdminCategoryHeaderMessages: ' . (count($result) - 1) . ' déclinaisons trouvées pour le produit: ' . $id_product, 1);
        } catch (Exception $e) {
            PrestaShopLogger::addLog('AdminCategoryHeaderMessages: erreur lors de la récupération des déclinaisons: ' . $e->getMessage(), 3);
        }
    } else {
        PrestaShopLogger::addLog('AdminCategoryHeaderMessages: ID de produit invalide ou manquant', 3);
    }
    
    // Assurez-vous que l'en-tête est correct pour éviter les problèmes CORS
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    die(json_encode($result));
}

    /**
     * Récupère les catégories sélectionnées pour ce message
     */
    private function getSelectedCategories()
    {
        if (!$this->object->id_message) {
            return [];
        }

        $categories = [];
        $sql = 'SELECT `id_category` 
                FROM `'._DB_PREFIX_.'category_header_message_category` 
                WHERE `id_message` = '.(int)$this->object->id_message;
        $result = Db::getInstance()->executeS($sql);
        
        if (is_array($result) && !empty($result)) {
            foreach ($result as $row) {
                $categories[] = $row['id_category'];
            }
        }
        
        return $categories;
    }

    /**
     * Processe le formulaire avant sauvegarde
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            // Vérifier les dates
            $date_start = Tools::getValue('date_start');
            $date_end = Tools::getValue('date_end');
            
            if (strtotime($date_end) < strtotime($date_start)) {
                $this->errors[] = $this->trans('La date de fin doit être postérieure à la date de début.', [], 'Admin.Notifications.Error');
                return false;
            }
        }
        
        $result = parent::postProcess();
        
        // Après sauvegarde, gérer les catégories et l'image
        if (Tools::isSubmit('submitAdd'.$this->table) && !count($this->errors) && isset($this->object)) {
            // Sauvegarder les catégories sélectionnées
            $this->updateCategories();
            
            // Gérer l'upload d'image
            $this->processImage();
        }
        
        return $result;
    }

    /**
     * Met à jour les catégories associées au message
     */
    private function updateCategories()
    {
        $id_message = (int)$this->object->id;
        
        // Supprimer les anciennes associations
        Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'category_header_message_category` 
            WHERE `id_message` = '.$id_message
        );
        
        // Ajouter les nouvelles associations
        $categories = Tools::getValue('categoryBox', []);
        if (!empty($categories)) {
            $values = [];
            foreach ($categories as $id_category) {
                $values[] = '('.$id_message.', '.(int)$id_category.')';
            }
            
            if (!empty($values)) {
                Db::getInstance()->execute('
                    INSERT INTO `'._DB_PREFIX_.'category_header_message_category` (`id_message`, `id_category`) 
                    VALUES '.implode(',', $values)
                );
            }
        }
    }

    /**
     * Gère l'upload et le traitement de l'image
     */
    private function processImage()
    {
        $message = $this->object;
        $id_message = (int)$message->id;
        
        // Si un fichier a été uploadé
        if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = 'message_'.$id_message.'.'.$ext;
            $img_dir = _PS_IMG_DIR_.'chm/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($img_dir)) {
                mkdir($img_dir, 0777, true);
            }
            
            // Supprimer l'ancienne image si elle existe
            $old_image = $message->getImage();
            if ($old_image && file_exists($img_dir.$old_image)) {
                unlink($img_dir.$old_image);
            }
            
            // Sauvegarder la nouvelle image
            if (move_uploaded_file($_FILES['image']['tmp_name'], $img_dir.$file_name)) {
                // Mettre à jour la référence de l'image dans la base de données
                $message->setImage($file_name);
            }
        }
    }
    
    /**
     * Récupère le nom de l'image pour un message
     */
    private function getImageName($id_message)
    {
        // Vérifier d'abord dans la table image standard
        $image_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'image"');
        if (!empty($image_table_exists)) {
            $entity_field_exists = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'image` LIKE "entity"');
            if (!empty($entity_field_exists)) {
                $sql = 'SELECT `name` 
                        FROM `'._DB_PREFIX_.'image` 
                        WHERE `entity` = "category_header_message" 
                        AND `id_entity` = '.(int)$id_message;
                
                $name = Db::getInstance()->getValue($sql);
                if ($name) {
                    return $name;
                }
            }
        }
        
        // Sinon, vérifier dans la table personnalisée
        $custom_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'category_header_message_image"');
        if (!empty($custom_table_exists)) {
            $sql = 'SELECT `name` 
                    FROM `'._DB_PREFIX_.'category_header_message_image` 
                    WHERE `id_message` = '.(int)$id_message;
            
            return Db::getInstance()->getValue($sql);
        }
        
        return false;
    }
    
    /**
     * Définit l'image associée pour un message
     */
    private function setImageName($id_message, $image_name)
    {
        // Vérifier quelle table utiliser
        $image_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'image"');
        if (!empty($image_table_exists)) {
            $entity_field_exists = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'image` LIKE "entity"');
            if (!empty($entity_field_exists)) {
                // Supprimer l'ancienne référence si elle existe
                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'image` 
                    WHERE `entity` = "category_header_message" 
                    AND `id_entity` = '.(int)$id_message
                );
                
                // Ajouter la nouvelle référence
                Db::getInstance()->execute('
                    INSERT INTO `'._DB_PREFIX_.'image` (`entity`, `id_entity`, `name`) 
                    VALUES ("category_header_message", '.(int)$id_message.', "'.pSQL($image_name).'")
                ');
                
                return true;
            }
        }
        
        // Sinon, utiliser la table personnalisée
        $custom_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'category_header_message_image"');
        if (!empty($custom_table_exists)) {
            // Supprimer l'ancienne référence si elle existe
            Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'category_header_message_image` 
                WHERE `id_message` = '.(int)$id_message
            );
            
            // Ajouter la nouvelle référence
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'category_header_message_image` (`id_message`, `name`) 
                VALUES ('.(int)$id_message.', "'.pSQL($image_name).'")
            ');
            
            return true;
        }
        
        return false;
    }
}

/**
 * Classe modèle pour un message commercial
 */
class CategoryHeaderMessage extends ObjectModel
{
    public $id_message;
    public $title;
    public $content;
    public $cta_text;
    public $cta_link;
    public $active;
    public $date_add;
    public $date_upd;
    public $date_start;
    public $date_end;
    public $position;
    public $type;
    public $id_product;
    public $id_product_attribute;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'category_header_message',
        'primary' => 'id_message',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'date_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'default' => 'standard'],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            
            // Champs multilingues
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true],
            'cta_text' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'cta_link' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'size' => 255],
        ]
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        // S'assurer que $id_message est toujours égal à $id
        //$this->id_message = $this->id;
    }

    public function add($auto_date = true, $null_values = false)
    {
        if ($auto_date) {
            $this->date_add = date('Y-m-d H:i:s');
        }
        
        $this->date_upd = date('Y-m-d H:i:s');
        
        $result = parent::add($auto_date, $null_values);
        
        // S'assurer que $id_message est toujours égal à $id
        $this->id_message = $this->id;
        
        return $result;
    }

    public function update($auto_date = true, $null_values = false)
    {
        $this->date_upd = date('Y-m-d H:i:s');
        
        // S'assurer que $id_message est toujours égal à $id
        $this->id_message = $this->id;
        
        return parent::update($auto_date, $null_values);
    }

    /**
     * Récupère le nom de l'image associée
     */
    public function getImage()
    {
        // Utiliser id_message comme identifiant
        $custom_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'category_header_message_image"');
        if (!empty($custom_table_exists)) {
            $sql = 'SELECT `name` 
                    FROM `'._DB_PREFIX_.'category_header_message_image` 
                    WHERE `id_message` = '.(int)$this->id_message;
            
            return Db::getInstance()->getValue($sql);
        }
        
        return false;
    }

    /**
     * Définit l'image associée
     */
    public function setImage($image_name)
    {
        // Utiliser id_message comme identifiant
        $custom_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'category_header_message_image"');
        if (!empty($custom_table_exists)) {
            // Remove any existing image references
            Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'category_header_message_image` 
                WHERE `id_message` = '.(int)$this->id_message
            );
            
            // Add the new image reference
            Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'category_header_message_image` (`id_message`, `name`) 
                VALUES ('.(int)$this->id_message.', "'.pSQL($image_name).'")
            ');
            
            return true;
        }
        
        return false;
    }

    /**
     * Supprime le message et ses données associées
     */
    public function delete()
    {
        // Utiliser id_message comme identifiant
        Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'category_header_message_category` 
            WHERE `id_message` = '.(int)$this->id_message
        );
        
        // Delete the image file
        $image = $this->getImage();
        if ($image) {
            $img_path = _PS_IMG_DIR_.'chm/'.$image;
            if (file_exists($img_path)) {
                unlink($img_path);
            }
            
            // Delete image references
            $custom_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'category_header_message_image"');
            if (!empty($custom_table_exists)) {
                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'category_header_message_image` 
                    WHERE `id_message` = '.(int)$this->id_message
                );
            }
        }
        
        return parent::delete();
    }
}
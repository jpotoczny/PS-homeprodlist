<?php

if (!defined('_PS_VERSION_'))
    exit;

class JpHomeProdList extends Module
{

    protected $config_form = false;
    protected static $cache_products;

    public function __construct()
    {
        $this->name = 'jphomeprodlist';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Jakub Potoczny';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Homepage products list');
        $this->description = $this->l('Product list with large images and descriptions for homepage');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->sourceCategory = Configuration::get('JPHOMEPRODLIST_CAT');
    }

    public function install()
    {
        $this->_clearCache('jphomeprodlist.tpl');
        Configuration::updateValue('JPHOMEPRODLIST_CAT', Configuration::get('PS_HOME_CATEGORY'));

        return parent::install() &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayHome') &&
                $this->registerHook('actionProductAdd') &&
                $this->registerHook('actionProductUpdate') &&
                $this->registerHook('actionProductDelete') &&
                $this->registerHook('actionCategoryUpdate');
    }

    public function uninstall()
    {
        $this->_clearCache('jphomeprodlist.tpl');
        Configuration::deleteByName('JPHOMEPRODLIST_CAT');

        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitJphomeprodlistModule')) == true)
            $this->postProcess();

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitJphomeprodlistModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Source category'),
                        'name' => 'JPHOMEPRODLIST_CAT',
                        'desc' => $this->l('Select category to show as featured products.'),
                        'tree' => array(
                            'id' => 'JPHOMEPRODLIST_CAT',
                            'selected_categories' => [$this->sourceCategory]
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'JPHOMEPRODLIST_CAT' => $this->sourceCategory,
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key)
            Configuration::updateValue($key, Tools::getValue($key));
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/jphomeprodlist.js');
        $this->context->controller->addCSS($this->_path . '/views/css/jphomeprodlist.css');
    }

    public function hookActionProductAdd()
    {
        $this->_clearCache('jphomeprodlist.tpl');
    }

    public function hookActionProductUpdate()
    {
        $this->_clearCache('jphomeprodlist.tpl');
    }

    public function hookActionProductDelete()
    {
        $this->_clearCache('jphomeprodlist.tpl');
    }

    public function hookActionCategoryUpdate()
    {
        $this->_clearCache('jphomeprodlist.tpl');
    }

    public function hookDisplayHome()
    {
        if (!$this->isCached('jphomeprodlist.tpl', $this->getCacheId())) {
            $this->_cacheProducts();
            $this->smarty->assign('products', JpHomeProdList::$cache_products);
        }
        return $this->display(__FILE__, 'jphomeprodlist.tpl', $this->getCacheId());
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache('jphomeprodlist.tpl');
    }

    public function _cacheProducts()
    {
        if (!isset(JpHomeProdList::$cache_products)) {
            $lang = (int) Context::getContext()->language->id;
            JpHomeProdList::$cache_products = (new Category($this->sourceCategory, $lang))
                    ->getProducts($lang, 1, 5, 'position');
            foreach (JpHomeProdList::$cache_products as $k => $p) {
                JpHomeProdList::$cache_products[$k]['images'] = (new Product($p['id_product']))->getImages($lang);
            }
        }
        if (JpHomeProdList::$cache_products === false || empty(JpHomeProdList::$cache_products))
            return false;
    }

}

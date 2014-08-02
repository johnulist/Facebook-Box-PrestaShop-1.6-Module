<?php

/*
 * BitSHOK Facebook Box
 * 
 * @author BitSHOK <office@bitshok.net>
 * @copyright 2012 BitSHOK
 * @version 1.0
 * @license http://creativecommons.org/licenses/by/3.0/ CC BY 3.0
 */

!defined('_PS_VERSION_') && exit;

class BskFacebookBox extends Module {

    public function __construct() {
        $this->name = 'bskfacebookbox'; // internal identifier, unique and lowercase
        $this->tab = 'social_networks'; // backend module coresponding category
        $this->version = '1.0'; // version number for the module
        $this->author = 'BitSHOK'; // module author
        $this->need_instance = 0; // load the module when displaying the "Modules" page in backend

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Facebook Box'); // public name
        $this->description = $this->l('Display Facebook box'); // public description
    }

    /*
     * Install this module
     */
    public function install() {
        return parent::install() &&
               $this->registerHook('displayHeader') &&
               $this->registerHook('displayFooter') &&
               $this->initConfig();
    }

    /*
     * Uninstall this module
     */
    public function uninstall() {
        return Configuration::deleteByName($this->name) &&
               Configuration::deleteByName($this->name . '_appId') &&
               parent::uninstall();
    }
    
    /**
     * Set the default values for Configuration page settings
     */
    protected function initConfig() {
        $config = array();
        
        $config['fbpage'] = 'bitshok';
        $config['width'] = '175';
        $config['height'] = '';
        $config['colorscheme'] = 'dark';
        $config['show_header'] = '0';
        $config['show_stream'] = '0';
        $config['show_faces'] = '1';
        $config['show_border'] = '0';
        $config['appId'] = '';
        
        return Configuration::updateValue($this->name, json_encode($config));
    }
    
    protected function getConfigFieldsValues() {
        return json_decode(Configuration::get($this->name), true);
    }

    /*
     * Header of pages hook (Technical name: displayHeader)
     */
    public function hookHeader() {
        $this->context->controller->addCSS($this->_path . 'style.css');

        $appId = Configuration::get($this->name . '_appId');
        $sdkLink = '//connect.facebook.net/en_US/all.js#xfbml=1';
        if (!empty($appId)) {
            $sdkLink .= "&appId={$appId}";
        }
        $this->context->smarty->assign('sdkLink', $sdkLink);
        return $this->display(__FILE__, 'bskfacebookbox_sdk.tpl');
    }

    public function hookFooter() {
        $config = json_decode(Configuration::get($this->name), true);
        
        $this->context->smarty->assign(array(
            'fbpage' => $config['fbpage'],
            'width' => $config['width'],
            'height' => $config['height'],
            'colorscheme' => $config['colorscheme'],
            'show_header' => $config['show_header'],
            'show_stream' => $config['show_stream'],
            'show_faces' => $config['show_faces'],
            'show_border' => $config['show_border'],
        ));
        
        return $this->display(__FILE__, 'bskfacebookbox_footer.tpl');
    }

    /**
     * Configuration page
     */
    public function getContent() {
        return $this->postProcess() . $this->renderForm();
    }
    
    protected function renderForm() {
        $colorscheme_options = array(
            array(
                'id_option' => 'light',
                'name'      => 'Light'
            ),
            array(
                'id_option' => 'dark',
                'name'      => 'Dark'
            )
        );
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Facebook box'),
                    'icon'  => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Page'),
                        'type'  => 'text',
                        'name'  => 'fbpage',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'label' => $this->l('Width'),
                        'type'  => 'text',
                        'name'  => 'width',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'label' => $this->l('Height'),
                        'type'  => 'text',
                        'name'  => 'height',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'label'     => $this->l('Select'),
                        'type'      => 'select',
                        'name'      => 'colorscheme',
                        'class'     => 'fixed-width-lg',
                        'options'   => array(
                            'query' => $colorscheme_options,
                            'id'    => 'id_option',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'label'     => $this->l('Show Header'),
                        'type'      => 'switch',
                        'name'      => 'show_header',
                        'values'    => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'label'     => $this->l('Show Stream'),
                        'type'      => 'switch',
                        'name'      => 'show_stream',
                        'values'    => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'label'     => $this->l('Show Faces'),
                        'type'      => 'switch',
                        'name'      => 'show_faces',
                        'values'    => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'label'     => $this->l('Show Border'),
                        'type'      => 'switch',
                        'name'      => 'show_border',
                        'values'    => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'label' => $this->l('App ID'),
                        'type'  => 'text',
                        'class' => 'fixed-width-lg',
                        'name'  => 'appId'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button pull-right'
                )
            )
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $helper->submit_action = 'saveBtn';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm(array($fields_form));
    }
    
    /*
     * Process data from Configuration page after form submition
     */
    protected function postProcess() {
        if (Tools::isSubmit('saveBtn')) {
            $config = array();
            
            $config['fbpage'] = Tools::getValue('fbpage');
            $config['width'] = Tools::getValue('width');
            $config['height'] = Tools::getValue('height');
            $config['colorscheme'] = Tools::getValue('colorscheme');
            $config['show_header'] = Tools::getValue('show_header');
            $config['show_stream'] = Tools::getValue('show_stream');
            $config['show_faces'] = Tools::getValue('show_faces');
            $config['show_border'] = Tools::getValue('show_border');
            $config['appId'] = Tools::getValue('appId');
            
            Configuration::updateValue($this->name, json_encode($config));
                        
            return $this->displayConfirmation($this->l('Settings updated'));
        }
    }

}

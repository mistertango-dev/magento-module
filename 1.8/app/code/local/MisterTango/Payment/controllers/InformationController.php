<?php

class MisterTango_Payment_InformationController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        /**
         * ---------------------------------------------------------------------------------------------------------
         */
        parent::initContent();

        if (!$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $authorized = false;

        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'mistertango') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        $order = new Order(Tools::getValue('id_order'));
        $cart = new Cart($order->id_cart);

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $mrTango = new MisterTango();

        $mrTango->assignTemplateAssets($this->context->smarty, $cart);
        $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/information.js');

        $mrTango->assignTemplateAssetsOrderStates($this->context->smarty, $order, $cart);

        $this->setTemplate('information.tpl');
        /**
         * ---------------------------------------------------------------------------------------------------------
         */
    }
}

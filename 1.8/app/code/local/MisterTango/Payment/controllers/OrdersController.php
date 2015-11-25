<?php

/**
 * Class MisterTango_Payment_OrdersController
 */
class MisterTango_Payment_OrdersController extends Mage_Core_Controller_Front_Action
{
    public function validateOrderAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {

            /**
             * ---------------------------------------------------------------------------------------------------------
             */
            if (!Validate::isLoadedObject($this->context->customer)) {
                die(Tools::jsonEncode(array(
                    'success' => false,
                    'error' => $this->module->l('You aren\'t logged in', 'mistertango'),
                )));
            }

            $id_transaction = Tools::getValue('id_transaction');
            $id_websocket = Tools::getValue('id_websocket');
            $amount = Tools::getValue('amount');

            $mrTango = new MisterTango();

            $isValidOrder = $mrTango->openOrder($id_transaction, $amount, $id_websocket);

            if ($isValidOrder) {
                die(Tools::jsonEncode(array(
                    'success' => true,
                    'id_order' => $mrTango->currentOrder,
                )));
            }
            /**
             * ---------------------------------------------------------------------------------------------------------
             */

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => false,
                    'error' => $this->__('Invalid transaction')
                ))
            );
        }
    }

    public function validateTransactionAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {

            /**
             * ---------------------------------------------------------------------------------------------------------
             */
            if (!Validate::isLoadedObject($this->context->customer)) {
                die(Tools::jsonEncode(array(
                    'success' => false,
                    'error' => $this->module->l('You aren\'t logged in', 'mistertango'),
                )));
            }

            $id_order = Tools::getValue('id_order');
            $id_transaction = Tools::getValue('id_transaction');
            $id_websocket = Tools::getValue('id_websocket');
            $amount = Tools::getValue('amount');

            $mrTango = new MisterTango();

            $order = new Order($id_order);

            if (Validate::isLoadedObject($order)) {
                $mrTango->addTransaction($id_transaction, $id_websocket, $order->id, $amount);

                die(Tools::jsonEncode(array(
                    'success' => true,
                    'id_order' => $order->id,
                )));
            }
            /**
             * ---------------------------------------------------------------------------------------------------------
             */

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => false,
                    'error' => $this->__('Order is invalid')
                ))
            );
        }
    }

    public function getHtmlTableOrderStatesAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {

            /**
             * ---------------------------------------------------------------------------------------------------------
             */
            if (!Validate::isLoadedObject($this->context->customer)) {
                die(Tools::jsonEncode(array(
                    'success' => false,
                    'error' => $this->module->l('You aren\'t logged in', 'mistertango'),
                )));
            }

            $id_order = Tools::getValue('id_order');

            $mrTango = new MisterTango();

            $order = new Order($id_order);

            if (Validate::isLoadedObject($order)) {
                $cart = new Cart($order->id_cart);

                $mrTango->assignTemplateAssets($this->context->smarty, $cart);
                $mrTango->assignTemplateAssetsOrderStates($this->context->smarty, $order, $cart);

                $path_table_order_states =
                    _PS_MODULE_DIR_
                    .$this->module->name
                    .'/views/templates/front/table_order_states.tpl';

                $path_themes_table_order_states =
                    _PS_THEME_DIR_
                    .'modules/'
                    .$this->module->name
                    .'/views/templates/front/table_order_states.tpl';

                if (file_exists($path_themes_table_order_states)) {
                    $path_table_order_states = $path_themes_table_order_states;
                }

                die(Tools::jsonEncode(array(
                    'success' => true,
                    'html_table_order_states' => $this->context->smarty->fetch($path_table_order_states),
                )));
            }
            /**
             * ---------------------------------------------------------------------------------------------------------
             */

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => false,
                    'error' => $this->__('Order is invalid')
                ))
            );
        }
    }
}

<?php

class shopPaymentSystem{

    private $order;
    private $inDB;

/* ========================================================================== */
/* ========================================================================== */

    /**
     * Получает всю информацию о заказе в массиве $order
     * и сохраняет внутри класса
     * @param array $order 
     */
    public function __construct($order){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ========================================================================== */
/* ========================================================================== */

    /**
     * Генерирует и возвращает код формы для отправки в платежную систему
     */
    public function getHtmlForm($currency){
        return true;
    }

/* ========================================================================== */
/* ========================================================================== */

    public function processPayment() {
        return true;
    }

/* ========================================================================== */
/* ========================================================================== */

}

?>
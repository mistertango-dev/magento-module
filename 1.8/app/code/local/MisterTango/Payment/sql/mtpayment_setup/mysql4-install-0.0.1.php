<?php

    $this->startSetup();
    $this->run(
        "CREATE TABLE IF NOT EXISTS `{$this->getTable('transactions_mistertango')}` (
            `transaction_id` varchar(255) NOT NULL,
            `amount` DECIMAL(10,2) NOT NULL,
            `order_id` int(10) NOT NULL,
            `websocket` varchar(255) NULL,
            `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`transaction_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $this->run(
        "CREATE TABLE IF NOT EXISTS `{$this->getTable('callbacks_mistertango')}` (
            `callback_uuid` VARCHAR(255) NOT NULL,
            `transaction_id` VARCHAR(255) NOT NULL,
            `amount` DECIMAL(10,2) NOT NULL,
            `callback_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`callback_uuid`));"
    );
    $this->endSetup();

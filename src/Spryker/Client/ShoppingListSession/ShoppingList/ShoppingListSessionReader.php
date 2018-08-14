<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ShoppingListSession\ShoppingList;

use Generated\Shared\Transfer\ShoppingListCollectionTransfer;
use Generated\Shared\Transfer\ShoppingListSessionTransfer;
use Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToCustomerClientBridgeInterface;
use Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToShoppingListBridgeInterface;
use Spryker\Client\ShoppingListSession\ShoppingListSessionPluginsExecutor\ShoppingListSessionPluginsExecutorInterface;
use Spryker\Client\ShoppingListSession\Storage\ShoppingListSessionStorageInterface;

class ShoppingListSessionReader implements ShoppingListSessionReaderInterface
{
    /**
     * @var \Spryker\Client\ShoppingListSession\Storage\ShoppingListSessionStorageInterface
     */
    protected $shoppingListSessionStorage;

    /**
     * @var \Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToShoppingListBridgeInterface
     */
    protected $shoppingListClient;

    /**
     * @var \Spryker\Client\ShoppingListSession\ShoppingListSessionPluginsExecutor\ShoppingListSessionPluginsExecutorInterface
     */
    protected $shoppingListSessionPluginsExecutor;

    /**
     * @var \Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToCustomerClientBridgeInterface
     */
    protected $customerClient;

    /**
     * @param \Spryker\Client\ShoppingListSession\Storage\ShoppingListSessionStorageInterface $shoppingListSessionStorage
     * @param \Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToShoppingListBridgeInterface $shoppingListClient
     * @param \Spryker\Client\ShoppingListSession\ShoppingListSessionPluginsExecutor\ShoppingListSessionPluginsExecutorInterface $shoppingListSessionPluginsExecutor
     * @param \Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToCustomerClientBridgeInterface $customerClient
     */
    public function __construct(
        ShoppingListSessionStorageInterface $shoppingListSessionStorage,
        ShoppingListSessionToShoppingListBridgeInterface $shoppingListClient,
        ShoppingListSessionPluginsExecutorInterface $shoppingListSessionPluginsExecutor,
        ShoppingListSessionToCustomerClientBridgeInterface $customerClient
    ) {
        $this->shoppingListSessionStorage = $shoppingListSessionStorage;
        $this->shoppingListClient = $shoppingListClient;
        $this->shoppingListSessionPluginsExecutor = $shoppingListSessionPluginsExecutor;
        $this->customerClient = $customerClient;
    }

    /**
     * @return \Generated\Shared\Transfer\ShoppingListCollectionTransfer
     */
    public function getCustomerShoppingListCollection(): ShoppingListCollectionTransfer
    {
        $customerTransfer = $this->customerClient->getCustomer();
        if (!$customerTransfer) {
            return new ShoppingListCollectionTransfer();
        }

        $shoppingListSessionTransfer = $this->shoppingListSessionStorage->findShoppingListCollection();
        if (!$shoppingListSessionTransfer ||
            $this->shoppingListSessionPluginsExecutor->executeCollectionOutdatedPlugins($shoppingListSessionTransfer)
        ) {
            $customerShoppingListCollectionTransfer = $this->shoppingListClient->getCustomerShoppingListCollection();
            $shoppingListSessionTransfer = (new ShoppingListSessionTransfer())
                ->setUpdatedAt(time())
                ->setShoppingLists($customerShoppingListCollectionTransfer);
            $this->shoppingListSessionStorage->setShoppingListCollection($shoppingListSessionTransfer);
        }

        return $shoppingListSessionTransfer->getShoppingLists();
    }
}

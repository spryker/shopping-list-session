<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ShoppingListSession;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToCustomerClientBridge;
use Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToSessionClientBridge;
use Spryker\Client\ShoppingListSession\Dependency\Client\ShoppingListSessionToShoppingListBridge;
use Spryker\Client\ShoppingListStorage\Dependency\Plugin\ShoppingListSession\ShoppingListCollectionOutdatedPlugin;

class ShoppingListSessionDependencyProvider extends AbstractDependencyProvider
{
    public const SHOPPING_LIST_SESSION_STORAGE_CLIENT = 'SHOPPING_LIST_SESSION_STORAGE_CLIENT';
    public const SHOPPING_LIST_SESSION_SESSION_CLIENT = 'SHOPPING_LIST_SESSION_SESSION_CLIENT';
    public const SHOPPING_LIST_SESSION_SHOPPING_LIST_CLIENT = 'SHOPPING_LIST_SESSION_SHOPPING_LIST_CLIENT';
    public const SHOPPING_LIST_SESSION_COLLECTION_OUTDATED_PLUGINS = 'SHOPPING_LIST_SESSION_COLLECTION_OUTDATED_PLUGINS';
    public const SHOPPING_LIST_SESSION_CUSTOMER_CLIENT = 'SHOPPING_LIST_SESSION_CUSTOMER_CLIENT';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = $this->addSessionClient($container);
        $container = $this->addShoppingListClient($container);
        $container = $this->addShoppingListCollectionOutdatedPlugins($container);
        $container = $this->addCustomerClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addSessionClient(Container $container): Container
    {
        $container[static::SHOPPING_LIST_SESSION_SESSION_CLIENT] = function (Container $container) {
            return new ShoppingListSessionToSessionClientBridge($container->getLocator()->session()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addShoppingListClient(Container $container): Container
    {
        $container[static::SHOPPING_LIST_SESSION_SHOPPING_LIST_CLIENT] = function (Container $container) {
            return new ShoppingListSessionToShoppingListBridge($container->getLocator()->shoppingList()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addShoppingListCollectionOutdatedPlugins(Container $container): Container
    {
        $container[static::SHOPPING_LIST_SESSION_COLLECTION_OUTDATED_PLUGINS] = function () {
            return $this->getShoppingListCollectionOutdatedPlugins();
        };

        return $container;
    }

    /**
     * @return \Spryker\Client\ShoppingListSessionExtension\Dependency\Plugin\ShoppingListCollectionOutdatedPluginInterface[]
     */
    protected function getShoppingListCollectionOutdatedPlugins(): array
    {
        return [
            new ShoppingListCollectionOutdatedPlugin(),
        ];
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container[static::SHOPPING_LIST_SESSION_CUSTOMER_CLIENT] = function (Container $container) {
            return new ShoppingListSessionToCustomerClientBridge($container->getLocator()->customer()->client());
        };

        return $container;
    }
}

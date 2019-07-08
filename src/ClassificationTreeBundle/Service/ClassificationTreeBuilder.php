<?php
/**
 * @date        12/01/2018
 * @author      Michał Bolka <mbolka@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\ClassificationTreeBundle\Service;

use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Product;

/**
 * Class ClassificationTreeBuilder
 * @package Divante\ClassificationTreeBundle\Service
 */
class ClassificationTreeBuilder
{
    /** @var StoreConfig\Listing $storeConfigListing */
    protected $storeConfigListing;

    /** @var TokenStorageUserResolver */
    protected $tokenStorageResolver;

    /**
     * @required
     * @param TokenStorageUserResolver $tokenStorageResolver
     */
    public function setTokenStorageResolver(TokenStorageUserResolver $tokenStorageResolver): void
    {
        $this->tokenStorageResolver = $tokenStorageResolver;
    }

    /**
     * @return array
     */
    public function getRootNodes()
    {
        $list = $this->getStoreConfigListing()->load();
        $result = [];

        /** @var $item StoreConfig */
        foreach ($list as $item) {
            $resultItem = [
                'allowChildren' => true,
                'allowDrop'     => 'false',
                'basePath'      => '/',
                'elementType'   => 'object',
                'expanded'      => false,
                'iconCls'       => 'pimcore_icon_classificationstore',
                'id'            => $item->getName(),
                'isTarget'      => true,
                'leaf'          => false,
                'locked'        => false,
                'path'          => '/',
                'permissions'   => $this->getPermission(),
                'qtipCfg'       => ['title' => 'ID: ' . $item->getName()],
                'text'          => $item->getName(),
                'type'          => 'folder'
            ];

            $result[]   = $resultItem;
        }

        return $result;
    }

    /**
     * @param string $nodeId
     * @param string $nodeName
     * @param string $classificationName
     * @param int $limit
     * @param int $start
     * @return array
     * @throws \Exception
     */
    public function getChildNodes($nodeId, $nodeName, $classificationName, $limit = 30, $start = 0)
    {
        if (strpos($nodeId, 'EC') === 0) {
            return $this->getClassificationCollections($nodeId, $limit, $start);
        }

        if (strpos($nodeId, 'EG') === 0) {
            return $this->getProductsFromGroup($this->getRawNodeName($nodeName), $classificationName, $limit, $start);
        }

        return $this->getClassificationGroups($nodeId, $limit, $start);
    }

    /**
     * Get node name without description part
     * @param string $nodeName
     * @return string
     */
    protected function getRawNodeName(string $nodeName): string
    {
        $nodeNameParts = explode("-", $nodeName);
        return trim($nodeNameParts[0]);
    }

    /**
     * @param int $nodeId
     * @param int $limit
     * @param int $start
     * @return array
     * @internal param $nodeName
     */
    public function getClassificationCollections($nodeId, $limit = 30, $start = 0)
    {

        $orderKey = 'name';
        $order    = 'ASC';
        $list     = new Classificationstore\CollectionGroupRelation\Listing();
        if (substr($nodeId, 0, 3) == "EC-") {
            $nodeId = substr($nodeId, 3, strlen($nodeId) - 3);
        }

        if ($limit > 0) {
            $list->setLimit($limit);
        }
        $list->setOffset($start);
        $list->setOrder($order);
        $list->setOrderKey($orderKey);
        $condition = ' colId = ' . $list->quote($nodeId);
        $list->setCondition($condition);
        $listItems = $list->load();
        $result    = [];
        /** @var Classificationstore\CollectionGroupRelation $config */
        foreach ($listItems as $config) {
            $resultItem = [
                'allowChildren' => true,
                'allowDrop'     => 'false',
                'basePath'      => '/',
                'elementType'   => 'object',
                'expanded'      => false,
                'iconCls'       => 'pimcore_icon_keys',
                'id'            => "EG-" . $config->getGroupId(),
                'isTarget'      => true,
                'leaf'          => false,
                'locked'        => false,
                'path'          => '/',
                'permissions'   => $this->getPermission(),
                'qtipCfg'       => ['title' => $config->getName()],
                'text'          => $config->getName() . ' - ' . $config->getDescription(),
                'type'          => 'folder'
            ];
            $result[]   = $resultItem;
        }
        return ['results' => $result, 'totalCount' => $list->getTotalCount()];
    }

    /**
     * @param string $nodeName
     * @param int $limit
     * @param int $start
     * @return array
     */
    public function getClassificationGroups($nodeName, $limit = 30, $start = 0)
    {
        $orderKey = 'name';
        $order    = 'ASC';

        $list = new Classificationstore\CollectionConfig\Listing();

        $list->setLimit($limit);
        $list->setOffset($start);
        $list->setOrder($order);
        $list->setOrderKey($orderKey);
        $storeId = StoreConfig::getByName($nodeName)->getId();
        $condition = '(storeId = ' . $storeId . ')';
        $list->setCondition($condition);
        $list->load();
        $configList = $list->getList();
        $totalCount = $list->getTotalCount();
        $result = [];

        /** @var Classificationstore\CollectionConfig $config */
        foreach ($configList as $config) {
            $name = $config->getName();
            if (!$name) {
                $name = 'EMPTY';
            }
            $item = [
                'storeId'     => $config->getStoreId(),
                'id'          => $config->getId(),
                'name'        => $name,
                'description' => $config->getDescription()
            ];
            if ($config->getCreationDate()) {
                $item['creationDate'] = $config->getCreationDate();
            }

            if ($config->getModificationDate()) {
                $item['modificationDate'] = $config->getModificationDate();
            }

            $resultItem = [
                'allowChildren' => true,
                'allowDrop'     => 'false',
                'basePath'      => '/',
                'elementType'   => 'object',
                'expanded'      => false,
                'iconCls'       => 'pimcore_icon_classificationstore_icon_cs_collections',
                'id'            => "EC-" . $config->getId(),
                'isTarget'      => true,
                'leaf'          => false,
                'locked'        => false,
                'path'          => '/',
                'permissions'   => $this->getPermission(),
                'qtipCfg'       => ['title' => $name],
                'text'          => $name . ' - ' . $config->getDescription(),
                'type'          => 'folder'
            ];
            $result[]   = $resultItem;
        }

        return ['results' => $result, 'totalCount' => $totalCount];
    }

    /**
     * @param string $nodeName
     * @param string $classificationName
     * @param int $limit
     * @param int $start
     * @return array
     * @throws \Exception
     */
    public function getProductsFromGroup($nodeName, $classificationName, $limit = 30, $start = 0)
    {
        $classId = $this->getClassId();

        $store = StoreConfig::getByName($classificationName);
        if (!$store instanceof StoreConfig) {
            return [
                'totalCount' => 0,
                'results'    => [],
            ];
        }

        $group = Classificationstore\GroupConfig::getByName($nodeName, $store->getId());
        if (!$group instanceof Classificationstore\GroupConfig) {
            return [
                'totalCount' => 0,
                'results'    => [],
            ];
        }

        $list = new Product\Listing();
        $list->setUnpublished(true);
        $list->setOrderKey('o_id')->setOrder('ASC');
        $list->setOffset($start)->setLimit($limit);

        $conditionFilters = [];

        $conditionFilters[] = <<<EOD
o_id IN (
    SELECT ocg.o_id
    FROM object_classificationstore_groups_{$classId} AS ocg
    WHERE ocg.groupId = {$group->getId()}
    UNION SELECT 0
)
EOD;

        if (!$this->getAdminUser()->isAdmin()) {
            $userIds         = $this->getAdminUser()->getRoles();
            $userIds[]       = $this->getAdminUser()->getId();
            $userIdsAsString = implode(',', $userIds);

            $conditionFilters[] = <<<EOD
(
    (
        select list from users_workspaces_object where userId in ($userIdsAsString)
        AND LOCATE(CONCAT(o_path,o_key),cpath)=1 ORDER BY LENGTH(cpath) DESC LIMIT 1
    )=1
OR
    (
        select list from users_workspaces_object where userId in ($userIdsAsString)
        AND LOCATE(cpath,CONCAT(o_path,o_key))=1 ORDER BY LENGTH(cpath) DESC LIMIT 1
    )=1
)
EOD;
        }

        $list->setCondition(implode(' AND ', $conditionFilters));
        $list->load();

        $result = [];

        /** @var Product $object */
        foreach ($list->getObjects() as $object) {
            $result[] = [
                'allowChildren' => true,
                'allowDrop'     => 'false',
                'basePath'      => '/',
                'elementType'   => 'object',
                'expanded'      => false,
                'iconCls'       => 'pimcore_icon_import_server',
                'id'            => $object->getId(),
                'isTarget'      => true,
                'leaf'          => true,
                'locked'        => false,
                'path'          => '/',
                'permissions'   => $this->getPermission($object),
                'qtipCfg'       => ['title' => $object->getKey()],
                'text'          => $object->getKey(),
                'type'          => 'object',
                'published'     => $object->isPublished(),
                'cls'           => $object->isPublished() ? '' : 'pimcore_unpublished'
            ];
        }

        return [
            'totalCount' => $list->getTotalCount(),
            'results'    => $result
        ];
    }

    /**
     * @param string $classificationName
     * @return string
     */
    protected function getEtimFieldname(string $classificationName): string
    {
        $classification  = StoreConfig::getByName($classificationName);
        $classDefinition = ClassDefinition::getByName("product");
        if (!$classification instanceof StoreConfig || !$classDefinition instanceof ClassDefinition) {
            return "";
        }
        $classificationId = $classification->getId();
        $context          = ['suppressEnrichment' => true];
        $definitions      = $classDefinition->getFieldDefinitions($context);

        /** @var ClassDefinition\Data $definition */
        foreach ($definitions as $definition) {
            /** @var ClassDefinition\Data\Classificationstore $definition */
            if ($definition->getFieldtype() == "classificationstore") {
                if ($definition->getStoreId() == $classificationId) {
                    return $definition->getName();
                }
            }
        }

        return "";
    }

    /**
     * @param string $classificationName
     * @return array
     */
    protected function getEtimFieldnames(string $classificationName): array
    {
        $classification  = StoreConfig::getByName($classificationName);
        $classDefinition = ClassDefinition::getByName("product");
        if (!$classification instanceof StoreConfig || !$classDefinition instanceof ClassDefinition) {
            return "";
        }
        $classificationId = $classification->getId();
        $context          = ['suppressEnrichment' => true];
        $definitions      = $classDefinition->getFieldDefinitions($context);

        $result = [];

        /** @var ClassDefinition\Data $definition */
        foreach ($definitions as $definition) {
            /** @var ClassDefinition\Data\Classificationstore $definition */
            if ($definition->getFieldtype() == "classificationstore") {
                if ($definition->getStoreId() == $classificationId) {
                    $result[] = $definition->getName();
                }
            }
        }

        return $result;
    }


    /**
     * @param Product|null $product
     * @return array
     */
    protected function getPermission(Product $product = null): array
    {
        if ($product instanceof Product) {
            return $product->getUserPermissions();
        }

        return [
            "save"       => false,
            "unpublish"  => false,
            "lEdit"      => false,
            "lView"      => false,
            "layouts"    => false,
            "list"       => true,
            "view"       => false,
            "publish"    => false,
            "delete"     => false,
            "rename"     => false,
            "create"     => false,
            "settings"   => false,
            "versions"   => false,
            "properties" => false,
            "lock"       => false,
            "unlock"     => false,
        ];
    }

    /**
     * @return \Pimcore\Model\User|null
     */
    protected function getAdminUser()
    {
        return $this->tokenStorageResolver->getUser();
    }

    /**
     * @return StoreConfig\Listing
     */
    protected function getStoreConfigListing()
    {
        return $this->storeConfigListing ?: new StoreConfig\Listing();
    }

    /**
     * @return int
     */
    protected function getClassId(): int
    {
        return (int) Product::classId();
    }
}

<?php
/**
 * @category    Wurth
 * @date        10/01/2018 18:13
 * @author      Michał Bolka <mbolka@divante.pl
 * @copyright   Copyright (c) 2017 Divante Ltd. (https://divante.co)
 */

namespace Divante\ClassificationTreeBundle\Migrations;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Db\Connection;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\MigrationManager;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\WebsiteSetting;
use PimcoreDevkitBundle\Model\CustomView;
use PimcoreDevkitBundle\Service\CustomViewService;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class Installer
 *
 * @package Divante\EtimBundle\Migrations
 */
class Installer extends MigrationInstaller
{
    /** @var CustomViewService  */
    protected $customViewService;

    /** @var  FileLocator */
    protected $fileLocator;
    const VIEW_NAME = 'Classification Tree';
    const CUSTOM_VIEW_SETTINGS = 'classificationTreeView';
    /**
     * Installer constructor.
     * @param BundleInterface $bundle
     * @param Connection $connection
     * @param MigrationManager $migrationManager
     * @param CustomViewService $customViewService
     * @param FileLocator $fileLocator
     */
    public function __construct(
        BundleInterface $bundle,
        Connection $connection,
        MigrationManager $migrationManager,
        CustomViewService $customViewService,
        FileLocator $fileLocator
    ) {
        $this->customViewService = $customViewService;
        $this->fileLocator = $fileLocator;
        parent::__construct($bundle, $connection, $migrationManager);
    }

    /**
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
        $classId = Product::classId();
        /** @var CustomViewService  $customViewService */
        $customView = $this->customViewService->createFromFile($this->locateCustomViewFilePath(), [
            'name'       => self::VIEW_NAME,
            'rootfolder' => '/',
            'classes'    => (string) $classId
        ]);

        $setting = WebsiteSetting::getByName(self::CUSTOM_VIEW_SETTINGS);

        if (!$setting instanceof WebsiteSetting) {
            $setting = new WebsiteSetting();
        }

        $setting->setValues([
            'name' => self::CUSTOM_VIEW_SETTINGS,
            'type' => 'text',
            'data' => $customView->getId(),
        ]);
        $setting->save();

        return $setting;
    }

    /**
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        $setting = WebsiteSetting::getByName(self::CUSTOM_VIEW_SETTINGS);
        if ($setting instanceof WebsiteSetting) {
            $customView = CustomView::getById($setting->getData());
            if ($customView instanceof CustomView) {
                $customView->delete();
            }
        }
    }

    /**
     * @return string
     */
    protected function locateCustomViewFilePath()
    {
        return $this->fileLocator->locate('@DivanteClassificationTreeBundle/Resources/customViews/channel.php');
    }
}

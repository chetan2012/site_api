<?php

namespace Drupal\site_api\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * JsonControllerPage to display JSON format of node.
 */
class JsonControllerPage extends ControllerBase {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a JsonControllerPage.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Function getPageJsonData.
   *
   * @param string $siteapikey
   *   The siteapikey.
   * @param int $nid
   *   The Node ID.
   */
  public function getPageJsonData($siteapikey, $nid) {
    if (!empty($nid)) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $node = $node_storage->load($nid)->toArray();
      return new JsonResponse($node, 200, ['Content-Type' => 'application/json']);
    }
    return [];
  }

  /**
   * Checks access for this controller.
   */
  public function access($siteapikey, $nid) {
    $config = $this->configFactory->get('system.site');
    $storedKey = $config->get('siteapikey');
    if (!empty($nid)) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $node = $node_storage->load($nid);
      if ($storedKey == 'No API Key yet' || $storedKey != $siteapikey || !is_numeric($nid) || $node->getType() != 'page') {
        // Return 403 Access Denied page.
        return AccessResult::forbidden();
      }
    }
    // Success result.
    return AccessResult::allowed();
  }

}

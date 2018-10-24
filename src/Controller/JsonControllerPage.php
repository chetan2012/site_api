<?php

namespace Drupal\site_api\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * JsonControllerPage to display JSON format of node.
 */
class JsonControllerPage extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a JsonControllerPage.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function get_page_json_data($siteapikey, $nid) {
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
    $config = \Drupal::config('system.site');
    $storedKey = $config->get('siteapikey');
    if (!empty($nid)) {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $node = $node_storage->load($nid);
      if ($storedKey == 'No API Key yet' || $storedKey != $siteapikey || !is_numeric($nid) || $node->getType() != 'page') {
        // Return 403 Access Denied page.  
        return AccessResult::forbidden();
      }
    }
    return AccessResult::allowed();
  }

}

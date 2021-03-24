<?php

namespace Drupal\axel_rest_node_json\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\node\Entity\Node;

/**
 * Defines class NodeDataController.
 */
class NodeDataController extends ControllerBase {
  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs ControllerBase.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory object.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $container->get('config.factory')
    );
  }

  /**
   * Sends json data of a node.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A json response.
   */
  public function index($apikey, $nid) {
    $config = $this->configFactory->get('system.site');
    if ($config->get('siteapikey') != $apikey) {
      throw new AccessDeniedHttpException();
    }
    return new JsonResponse([
      'data' => $this->getData($nid),
      'method' => 'GET',
      'status' => 200,
    ]);
  }

  /**
   * Get node data.
   *
   * @return array
   *   Returns node data in array format.
   */
  public function getData($nid) {

    $result = [];
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'page')
      ->condition('nid', $nid);
    $nodes_ids = $query->execute();
    if ($nodes_ids) {
      foreach ($nodes_ids as $node_id) {
        $node = Node::load($node_id);
        $result[] = $node->toArray();
      }
    }
    else {
      throw new AccessDeniedHttpException();
    }
    return $result;
  }

}

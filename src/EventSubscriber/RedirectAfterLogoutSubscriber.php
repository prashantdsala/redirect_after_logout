<?php

namespace Drupal\redirect_after_logout\EventSubscriber;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * RedirectAfterLogoutSubscriber event subscriber.
 *
 * @package Drupal\redirect_after_logout\EventSubscriber
 */
class RedirectAfterLogoutSubscriber extends ControllerBase implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Check redirection.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   Event.
   */
  public function checkRedirection(FilterResponseEvent $event) {
    $response = $event->getResponse();
    if ($response instanceof RedirectResponse) {
      $destination = &drupal_static('redirect_after_logout_user_logout');
      if ($destination) {
        $config = $this->configFactory->get('redirect_after_logout.settings');
        $logout_message = $config->get('redirect_after_logout_message', '');
        if (!empty($logout_message)) {
          // TODO Needs refactoring.
          $destination = $destination . '?logout-message=1';
        }
        $response->setTargetUrl($destination);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['checkRedirection'];
    return $events;
  }

}

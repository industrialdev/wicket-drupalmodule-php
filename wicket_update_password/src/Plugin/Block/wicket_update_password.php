<?php
/**
 * @file
 * Contains \Drupal\wicket_update_password\Plugin\Block\YourBlockName.
 */
namespace Drupal\wicket_update_password\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\wicket_update_password\Form\wicketUpdatePasswordForm;
/**
 * Provides hiring block.
 *
 * @Block(
 *   id = "wicket_update_password",
 *   admin_label = @Translation("Update Password Block"),
 *   category = @Translation("Custom")
 * )
 */
class wicket_update_password extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form_class = '\Drupal\wicket_update_password\Form\wicketUpdatePasswordForm';
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);

    return [
      'form' => $build
    ];

    return array('#markup' => $return);
  }
}

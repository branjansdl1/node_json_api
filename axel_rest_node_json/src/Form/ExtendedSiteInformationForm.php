<?php

namespace Drupal\axel_rest_node_json\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Class to extend SiteInformationForm.
 */
class ExtendedSiteInformationForm extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config('system.site');
    $form = parent::buildForm($form, $form_state);
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#default_value' => $site_config->get('siteapikey') ?: 'No API Key yet',
      '#description' => t("Custom field to set the API Key"),
    ];
    if ($site_config->get('siteapikey') != NULL) {
      $form['actions']['submit']['#value'] = 'Update Configuration';
    }
    return $form;
  }

  /**
   * Submit handler for siteapikey save.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('siteapikey'))
      ->save();

    if (!empty($this->config('system.site')->get('siteapikey'))) {
      $messenger = \Drupal::messenger();
      $messenger->addMessage('Site API Key is saved with value ' .
        $this->config('system.site')->get('siteapikey'), $messenger::TYPE_STATUS);
    }

    parent::submitForm($form, $form_state);
  }

}

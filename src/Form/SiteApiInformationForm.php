<?php

namespace Drupal\site_api\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Configure site information settings for this site.
 */
class SiteApiInformationForm extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Retrieve the system.site configuration
    $site_config = $this->config('system.site');

    // Get the original form from the class we are extending
    $form = parent::buildForm($form, $form_state);

    // Add a textfield to the site information section of the form for our
    // siteapikey
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#default_value' => $site_config->get('siteapikey'),
    ];

    if (!empty($site_config->get('siteapikey')) && $site_config->get('siteapikey') != 'No API Key yet') {
      $form['actions']['submit']['#value'] = 'Update Configuration';
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('system.site')
        ->set('siteapikey', $form_state->getValue('siteapikey'))
        ->save();

    // Pass the remaining values off to the original form that we have extended,
    // so that they are also saved.
    parent::submitForm($form, $form_state);

    if (!empty($form_state->getValue('siteapikey')) && $form_state->getValue('siteapikey') != "No API Key yet") {
      drupal_set_message($this->t('The Site API Key has been saved to @siteapikey', array('@siteapikey' => $form_state->getValue('siteapikey'))), 'status');
    }
  }

}

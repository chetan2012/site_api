<?php

namespace Drupal\Tests\site_api\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Integration test for the site configuration form site api field.
 *
 * @group site_api
 */
class ConfigurationTest extends BrowserTestBase {

  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'user',
    'site_api',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($user);
  }

  /**
   * Test site information form site api field.
   */
  public function testFieldSettingsForm() {
    $edit = [
      'siteapikey' => 'FOOBAR12345',
    ];
    $this->drupalPostForm('system/site-information', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'Configuration options have been saved');
  }

  /**
   * Test site information form site api field after adding the value.
   */
  public function testFieldSettingsFormupdate() {
    $edit = [
      'siteapikey' => 'FOOBAR123456',
    ];
    $this->drupalPostForm('system/site-information', $edit, t('Update configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'Configuration options have been saved');
  }

}

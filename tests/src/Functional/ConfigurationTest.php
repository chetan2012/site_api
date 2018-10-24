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
   * The path to a node that is created for testing.
   *
   * @var string
   */
  protected $nodePath;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'user',
    'site_api',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalLogin($this->drupalCreateUser(
      [
        'access administration pages',
        'administer site configuration',
      ]
    ));
    $this->drupalCreateContentType(['type' => 'page']);
    $this->nodePath = "node/" . $this->drupalCreateNode(['promote' => 1])->id();
  }

  /**
   * Test site information form site api field.
   */
  public function testFieldSettingsForm() {
    $this->drupalGet('admin/config/system/site-information');
    $this->assertFieldById('edit-siteapikey', 'No API Key yet');
    $edit = [
      'siteapikey' => 'FOOBAR12345',
      'site_frontpage' => '/' . $this->nodePath,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'The Site API Key has been saved to @siteapi.', [@siteapi => $edit['siteapikey']]);

    // After adding the value update the field.
    $edit['siteapikey'] = 'FOOBAR123456';
    $this->drupalPostForm(NULL, $edit, t('Update configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'The Site API Key has been saved to @siteapi.', [@siteapi => $edit['siteapikey']]);
  }

}

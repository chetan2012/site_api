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
   * The id to a node that is created for testing.
   *
   * @var int
   */
  protected $nodeid;

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
    $this->nodeid = $this->drupalCreateNode(['promote' => 1])->id();
    $this->nodePath = "node/" . $this->nodeid;
  }

  /**
   * Test site information form site api field.
   */
  public function testFieldSettingsForm() {
    $this->drupalGet('admin/config/system/site-information');
    $this->assertFieldById('edit-siteapikey', 'No API Key yet');
    $edit_save = [
      'siteapikey' => 'FOOBAR12345',
      'site_frontpage' => '/' . $this->nodePath,
    ];
    $this->drupalPostForm(NULL, $edit_save, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'The Site API Key has been saved to @siteapi.', [@siteapi => $edit_save['siteapikey']]);

    // After adding the value update the field.
    $edit_update['siteapikey'] = 'FOOBAR123456';
    $this->drupalPostForm(NULL, $edit_update, t('Update configuration'));
    $this->assertText(t('The configuration options have been saved.'), 'The Site API Key has been saved to @siteapi.', [@siteapi => $edit_update['siteapikey']]);

    // Check the json page is exist with 403 response.
    $this->drupalGet('page_json/' . $edit_save['siteapikey'] . '/' . $this->nodeid);
    $this->assertText(t('Access denied'));
    $this->assertResponse(403);

    // Check the json page is exist with 200 response.
    $this->drupalGet('page_json/' . $edit_update['siteapikey'] . '/' . $this->nodeid);
    $this->assertResponse(200);

    // Logout and check that the configuration page is showing default 
	// 403 pages.
    $this->drupalLogout();
    $this->drupalGet('admin/config/system/site-information');
    $this->assertText(t('Access denied'));
    $this->assertResponse(403);
  }

}
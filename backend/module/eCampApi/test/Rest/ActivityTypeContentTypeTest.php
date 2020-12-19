<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityTypeContentTypeTest extends AbstractApiControllerTestCase {
    /** @var ActivityType */
    protected $activityType;

    /** @var ActivityTypeContentType */
    protected $activityTypeContentType;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activity-type-content-types';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityTypeLoader = new ActivityTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activityType = $activityTypeLoader->getReference(ActivityTypeTestData::$TYPE1);
        $this->activityTypeContentType = $this->activityType->getActivityTypeContentTypes()[0];

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $id = $this->activityTypeContentType->getId();
        $this->dispatch("{$this->apiEndpoint}/{$id}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$id}",
                "defaultInstances": 0
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activityTypeContentType->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['activityType', 'contentType'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $this->dispatch("{$this->apiEndpoint}?page_size=10&activityTypeId={$this->activityType->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&activityTypeId={$this->activityType->getId()}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activityTypeContentType->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityTypeContentType->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityTypeContentType->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityTypeContentType->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}

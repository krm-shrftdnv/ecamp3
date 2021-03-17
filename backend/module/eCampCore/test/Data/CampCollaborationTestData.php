<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;

class CampCollaborationTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $COLLAB1 = CampCollaboration::class.':COLLAB1';
    public static $COLLAB_INVITED = CampCollaboration::class.':COLLAB_INVITED';
    public static $COLLAB_INACTIVE = CampCollaboration::class.':COLLAB_INACTIVE';

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        /** @var User $user */
        $user = $this->getReference(UserTestData::$USER1);

        $campCollaboration = new CampCollaboration();
        $campCollaboration->setCamp($camp);
        $campCollaboration->setUser($user);
        $campCollaboration->setRole(CampCollaboration::ROLE_MEMBER);
        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);

        $manager->persist($campCollaboration);
        $manager->flush();

        $this->addReference(self::$COLLAB1, $campCollaboration);

        $campCollaborationInvited = new CampCollaboration();
        $campCollaborationInvited->setCamp($camp);
        $campCollaborationInvited->setInviteEmail('e.mail@test.com');
        $campCollaborationInvited->setRole(CampCollaboration::ROLE_GUEST);
        $campCollaborationInvited->setStatus(CampCollaboration::STATUS_INVITED);
        $campCollaborationInvited->setInviteKey('myInviteKey');

        $manager->persist($campCollaborationInvited);
        $manager->flush();

        $this->addReference(self::$COLLAB_INVITED, $campCollaborationInvited);

        $campCollaborationInactive = new CampCollaboration();
        $campCollaborationInactive->setCamp($camp);
        $campCollaborationInactive->setInviteEmail('e.mail.inactive@test.com');
        $campCollaborationInactive->setRole(CampCollaboration::ROLE_GUEST);
        $campCollaborationInactive->setStatus(CampCollaboration::STATUS_INACTIVE);

        $manager->persist($campCollaborationInactive);
        $manager->flush();

        $this->addReference(self::$COLLAB_INACTIVE, $campCollaborationInactive);
    }

    public function getDependencies() {
        return [CampTestData::class, UserTestData::class];
    }
}

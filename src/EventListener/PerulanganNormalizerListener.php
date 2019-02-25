<?php

namespace App\EventListener;

use App\Entity\Pengingat;
use Doctrine\ORM\Event\OnFlushEventArgs;

class PerulanganNormalizerListener
{
    /**
     * @param OnFlushEventArgs $eventArgs Event args
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $updatedEntities = $uow->getScheduledEntityUpdates();

        foreach ($updatedEntities as $updatedEntity) {
            if ($updatedEntity instanceof Pengingat) {
                $changeset = $uow->getEntityChangeSet($updatedEntity);

                if (!is_array($changeset)) {
                    return null;
                }

                if (array_key_exists('perulangan', $changeset)) {
                    $changes = $changeset['perulangan'];

                    $previousValue = array_key_exists(0, $changes) ? $changes[0] : null;

                    if ($previousValue == Pengingat::PENGINGAT_MINGGUAN) {
                        $updatedEntity->setMingguanHariKe(null);
                    } elseif ($previousValue == Pengingat::PENGINGAT_BULANAN) {
                        $updatedEntity->setBulananHariKe(null);
                    }

                    $em->persist($updatedEntity);
                    $metadata = $em->getClassMetadata(Pengingat::class);
                    $uow->recomputeSingleEntityChangeSet($metadata, $updatedEntity);
                }
            }
        }
    }
}

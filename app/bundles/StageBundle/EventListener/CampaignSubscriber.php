<?php

namespace Mautic\StageBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Entity\LeadEventLog;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\PendingEvent;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\StageBundle\Entity\Stage;
use Mautic\StageBundle\Form\Type\StageActionChangeType;
use Mautic\StageBundle\Helper\StageHelper;
use Mautic\StageBundle\Model\StageModel;
use Mautic\StageBundle\StageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    private StageModel $stageModel;

    private LeadModel $leadModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        TranslatorInterface $translator,
        StageModel $stageModel,
        LeadModel $leadModel
    ) {
        $this->translator = $translator;
        $this->stageModel = $stageModel;
        $this->leadModel  = $leadModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD     => ['onCampaignBuild', 0],
            StageEvents::ON_CAMPAIGN_BATCH_ACTION => ['onCampaignTriggerStageChange', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $action = [
            'label'            => 'mautic.stage.campaign.event.change',
            'description'      => 'mautic.stage.campaign.event.change_descr',
            'batchEventName'   => StageEvents::ON_CAMPAIGN_BATCH_ACTION,
            'formType'         => StageActionChangeType::class,
            'formTheme'        => '@MauticStage/FormTheme/Action/_stageaction_properties_row.html.twig',
        ];
        $event->addAction('stage.change', $action);
    }

    public function onCampaignTriggerStageChange(PendingEvent $event): void
    {
        $logs    = $event->getPending();
        $config  = $event->getEvent()->getProperties();
        $stageId = (int) $config['stage'];
        $stage   = $this->stageModel->getStage($stageId);

        if (null === $stage || !$stage->isPublished()) {
            $event->passAllWithError($this->translator->trans('mautic.stage.campaign.event.stage_missing'));

            return;
        }

        foreach ($logs as $log) {
            $this->changeStage($log, $stage, $event);
        }
    }

    private function changeStage(LeadEventLog $log, Stage $stage, PendingEvent $pendingEvent): void
    {
        $lead      = $log->getLead();

        try {
            $this->leadModel->changeStage($lead, $stage, $log->getEvent()->getName());
            $pendingEvent->pass($log);
        } catch (\UnexpectedValueException $e) {
            $pendingEvent->passWithError($log, $e->getMessage());
        }
    }
}

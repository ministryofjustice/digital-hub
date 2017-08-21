<?php

namespace Drupal\moj_search;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

class ResultsLinksService extends ControllerBase
{

    protected $node_storage;

    protected $entityManager;

    protected $entity;

    /**
     * ResultsLinksService constructor.
     *
     * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
     */

    public function __construct()
    {
        $this->entityManager = $ResultsLinksService = \Drupal::service(
          'entity.manager'
        ); // TODO: Inject dependency
        $this->node_storage = $this->entityManager->getStorage('node');
    }

    /**
     * @param string $type
     * @param int $nid
     *
     * @return string
     */

    public function generatelinks($entity)
    {
        switch ($entity->getType()) {
            case 'moj_video_item':
                return '/video/'.$entity->id();
                break;
            case 'moj_radio_item':
                return '/radio/'.$entity->id();
                break;
            case 'moj_pdf_item':
                return $this->generatepdflink($entity);
                break;
        }
    }

    /**
     * @param int $nid
     *
     * @return string
     */

    private function generatepdflink($entity)
    {
        $url = parse_url(file_create_url($entity->get('field_moj_pdf')->entity->getFileUri()));
        $this->entity = [
          'mimeType' => $entity->get('field_moj_pdf')->entity->getMimeType(),
          'filePath' => $url['path']
        ];
        return $this->checkFileIsPdfOrEpub();
    }

    private function checkFileIsPdfOrEpub()
    {
        return $this->entity['mimeType'] === 'application/epub+zip' ? '/epub?pdf='.$this->entity['filePath'] : $this->entity['filePath'];
    }
}
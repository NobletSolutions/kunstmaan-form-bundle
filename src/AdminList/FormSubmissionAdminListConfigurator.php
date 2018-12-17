<?php

namespace NS\KunstmaanFormBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

use Doctrine\ORM\QueryBuilder;

/**
 * Adminlist configuration to list all the form submissions for a given NodeTranslation
 */
class FormSubmissionAdminListConfigurator extends \Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator
{
    public function canDelete($item)
    {
        return true;
    }
    
    /**
     * Get the delete url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = array('nodeTranslationId' => $this->nodeTranslation->getId(), 'submissionId' => $item->getId());
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params
        );
    }
}

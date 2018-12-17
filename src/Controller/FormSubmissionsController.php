<?php

namespace NS\KunstmaanFormBundle\Controller;

use Ddeboer\DataImport\Writer\CsvWriter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use NS\KunstmaanFormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The controller which will handle everything related with form pages and form submissions
 */
class FormSubmissionsController extends \Kunstmaan\FormBundle\Controller\FormSubmissionsController
{
    /**
     * The list action will use an admin list to list all the form submissions related to the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/list/{nodeTranslationId}", requirements={"nodeTranslationId" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function listAction($nodeTranslationId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        /* @var $adminList AdminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList(new FormSubmissionAdminListConfigurator($em, $nodeTranslation), $em);
        $adminList->bindRequest($request);

        return array('nodetranslation' => $nodeTranslation, 'adminlist' => $adminList);
    }
    
    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/delete/{nodeTranslationId}/{submissionId}", requirements={"nodeTranslationId" = "\d+", "submissionId" = "\d+"}, name="kunstmaanformbundle_admin_formsubmission_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction($nodeTranslationId, $submissionId)
    {
        $em              = $this->getDoctrine()->getManager();
        $request         = $this->getRequest();
        $formSubmission  = $em->getRepository('KunstmaanFormBundle:FormSubmission')->find($submissionId);
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        $configurator    = new FormSubmissionAdminListConfigurator($em, $nodeTranslation);
        
        //Doing this manually because FormSubmissionFieldRepository is missing for some insane reason
        $stmt = "DELETE FROM kuma_form_submission_fields WHERE form_submission_id = :id";
        $params = array('id'=>$formSubmission->getId());
        
        $em->getConnection()->prepare($stmt)->execute($params);
        
        $em->remove($formSubmission);
        $em->flush();

        $indexUrl = $configurator->getIndexUrl();

        return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
    }
}